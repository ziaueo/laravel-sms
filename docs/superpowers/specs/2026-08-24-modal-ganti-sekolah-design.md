# Modal Ganti Sekolah

Tanggal: 2026-08-24

## Ringkasan

Memindahkan aksi **ganti sekolah** dari navigasi halaman penuh ke sebuah modal yang bisa
dibuka dari mana saja di dalam aplikasi. Halaman `/select-school` tetap ada dan tidak diubah,
karena masih dipakai untuk kasus wajib-pilih setelah login.

Sekalian menutup celah otorisasi di `SchoolSwitchController::store()` yang ditemukan saat
merancang fitur ini.

## Masalah

`/select-school` sekarang melayani dua situasi yang berbeda sifatnya:

1. **Wajib pilih** — setelah login ketika user punya lebih dari satu sekolah
   (`LoginController::redirectAfterLogin`), atau ketika `CheckActiveSchool` menemukan session
   kosong. Pada titik ini `active_school()` masih `null`, sehingga navbar dan sidebar belum bisa
   dirender. Karena itu halaman ini memakai `layouts.auth`.

2. **Ganti sekolah sukarela** — dari tombol "Ganti Sekolah" di navbar
   (`resources/views/components/navbar.blade.php:36`) dan kartu sekolah di sidebar
   (`resources/views/components/sidebar.blade.php:182`), ketika user sudah berada di dalam
   aplikasi dengan sekolah aktif.

Situasi kedua tidak perlu meninggalkan halaman. User kehilangan konteks tempatnya bekerja hanya
untuk memilih satu item dari daftar pendek.

### Celah otorisasi

```php
public function store(Request $request)
{
    $request->validate(['school_id' => 'required|exists:schools,id']);
    session(['active_school_id' => $request->school_id]);
    return redirect()->route('dashboard');
}
```

Validasinya hanya memastikan sekolah itu **ada**, bukan bahwa user **berhak** atasnya.
`CheckActiveSchool` juga hanya mengecek `session('active_school_id')` terisi, bukan sah.
Akibatnya user mana pun yang sudah login bisa mengirim `school_id` apa saja dan berpindah ke
data sekolah tersebut. Karena file ini memang diubah untuk fitur modal, perbaikannya
dikerjakan sekalian.

## Keputusan

| Hal | Keputusan |
|---|---|
| Cakupan modal | Hanya ganti sekolah sukarela. Flow login tidak disentuh sama sekali |
| Halaman `/select-school` | Dipertahankan apa adanya untuk kasus wajib-pilih |
| Setelah memilih | Selalu redirect ke dashboard (perilaku `store()` sekarang) |
| Sumber daftar | Endpoint mengembalikan **potongan HTML** hasil render Blade, bukan JSON |
| Pencarian | Server-side, lewat query param `q` |
| Interaksi | Klik kartu sekolah langsung berpindah. Tanpa radio, tanpa tombol konfirmasi |
| Sekolah aktif | Ditandai badge "AKTIF" dan tidak bisa diklik |

### Kenapa redirect selalu ke dashboard

Bertahan di URL yang sama terdengar lebih nyaman, tapi rusak untuk halaman detail dan edit:
`/students/5/edit` atau `/report-cards/12` merujuk ID milik sekolah lama. Setelah pindah, ID
tersebut bukan milik sekolah aktif — hasilnya 404 atau, lebih buruk, data bocor lintas sekolah.
Dashboard adalah satu-satunya tujuan yang selalu sah untuk sekolah mana pun.

### Kenapa HTML, bukan JSON

Proyek ini seluruhnya Blade dengan JavaScript vanilla; tidak ada satu pun pola JS templating di
dalamnya. Mengembalikan JSON berarti markup kartu sekolah hidup di dua tempat — Blade untuk
halaman `/select-school`, template string JS untuk modal. Mengembalikan HTML membuat semua
markup tetap di Blade, sesuai kebiasaan proyek.

### Kenapa pencarian di server

Untuk super admin, daftar sekolah adalah seluruh sekolah aktif di sistem. Jumlahnya bisa tumbuh
tanpa batas yang bisa kita ketahui sekarang. Pencarian server-side membuat modal tetap ringan
berapa pun jumlah sekolahnya, tanpa perlu dirombak nanti.

## Arsitektur

Modal dipasang sekali di `layouts/app.blade.php`, jadi tersedia di setiap halaman yang sudah
login. Isinya kosong sampai dibuka; daftar sekolah baru diambil pada saat itu.

Cangkang modal (statis) dipisah dari daftar sekolah (dinamis), karena yang kedua adalah satu-
satunya bagian yang dikembalikan endpoint dan ditukar berulang kali selama user mengetik.

### Berkas baru

| Berkas | Tanggung jawab |
|---|---|
| `resources/views/components/school-switcher-modal.blade.php` | Cangkang: backdrop, header, kotak cari, wadah daftar kosong, form POST tersembunyi |
| `resources/views/components/school-switcher-items.blade.php` | Hanya kartu-kartu sekolah. Satu-satunya keluaran endpoint |
| `resources/js/components/school-switcher.js` | Buka/tutup, fetch, debounce pencarian, klik untuk berpindah |
| `resources/css/components/school-switcher.css` | Gaya kartu sekolah versi modal |
| `tests/Feature/SchoolSwitchTest.php` | Uji scoping daftar dan otorisasi perpindahan |
| `database/factories/SchoolFactory.php` | Prasyarat test |
| `database/factories/SchoolTypeFactory.php` | Prasyarat test |

### Berkas yang diubah

| Berkas | Perubahan |
|---|---|
| `app/Http/Controllers/Web/Auth/SchoolSwitchController.php` | Tambah `list()`, tarik `accessibleSchools()`, perketat `store()` |
| `app/Helpers/helpers.php` | Tambah `can_switch_school()` |
| `app/Models/School.php` | Tambah trait `HasFactory` (prasyarat factory) |
| `app/Models/SchoolType.php` | Tambah trait `HasFactory` (prasyarat factory) |
| `routes/web.php` | Tambah `GET /select-school/list` |
| `resources/views/layouts/app.blade.php` | Include modal |
| `resources/views/components/navbar.blade.php` | Tombol jadi pemicu modal |
| `resources/views/components/sidebar.blade.php` | Kartu sekolah jadi pemicu modal |
| `resources/css/components/sidebar.css` | Kartu sekolah tanpa `href` jadi non-interaktif |
| `resources/js/app.js` | Import komponen baru |
| `resources/css/app.css` | Import CSS baru |
| `phpunit.xml` | Setel `APP_URL` supaya `route()` bisa diuji |

Kedua berkas view `school-switcher-*` diletakkan di `resources/views/components/` mengikuti pola
`navbar.blade.php` dan `sidebar.blade.php` — partial biasa yang di-`@include`, bukan komponen
`<x-...>`. Keduanya tidak memakai `@props`.

`resources/js/components/` sudah jadi pola di proyek ini (`sidebar.js`, `theme.js`, `select2.js`,
`datatable.js`), jadi `school-switcher.js` masuk ke situ.

### Kenapa CSS baru, bukan `.school-option`

Kelas `.school-option` milik halaman `/select-school` ada di `resources/css/layouts/auth.css` dan
sebenarnya ikut termuat di layout app, karena `app.css` mengimpor semuanya jadi satu bundle. Tapi
gayanya terikat radio (`.school-option:has(input:checked)`). Modal ini klik-langsung tanpa radio,
jadi memakai kelas sendiri lebih jujur daripada memaksa kelas milik halaman lain.

## Controller

```php
private const LIST_LIMIT = 50;

private function accessibleSchools(): Builder
{
    $user  = Auth::user();
    $query = School::query()->where('is_active', true);

    if ($user->hasRole('super_admin')) {
        return $query;
    }

    return $query->whereIn('id', $user->userSchools()->select('school_id'));
}
```

Aturan siapa-melihat-apa sekarang tertulis sekali dan dipakai bersama oleh `index()`, `list()`,
dan `store()`.

Perhatikan bahwa ini memakai `whereIn` atas subquery `userSchools`, bukan relasi
`$user->schools()` yang dipakai `index()` sebelumnya. Alasannya: `user_schools` punya unique key
`(user_id, school_id, role)`, sehingga satu user yang memegang dua peran di sekolah yang sama
menghasilkan dua baris pivot — dan `belongsToMany` akan memunculkan sekolah itu dua kali di
daftar. `whereIn` tidak bisa menghasilkan duplikat. Ini sekaligus membuat tipe kembaliannya
selalu `Builder`.

### `list()`

```
GET /select-school/list?q=mekar
```

- Validasi: `q` → `nullable|string|max:100`
- Filter: `where('name', 'like', "%{$q}%")` bila `q` terisi
- `with('schoolType')` — view memanggil `$school->schoolType->name` per baris; tanpa eager load ini N+1
- `orderBy('name')`, `limit(LIST_LIMIT)`
- Mengembalikan `view('components.school-switcher-items', ['schools' => ..., 'isLimited' => ...])`
- `isLimited` bernilai `true` ketika jumlah hasil persis sama dengan `LIST_LIMIT`, dipakai untuk
  menampilkan ajakan mempersempit pencarian. Tidak ada query `count()` tambahan.

### `store()`

```php
$allowed = $this->accessibleSchools()->whereKey($request->school_id)->exists();

if (! $allowed) {
    abort(403, 'Kamu tidak memiliki akses ke sekolah ini.');
}
```

Karena `accessibleSchools()` sudah menyaring `is_active`, pengecekan ini sekaligus menutup
perpindahan ke sekolah yang sudah dinonaktifkan.

Nilai yang disimpan di-cast ke `int`. Input form selalu berupa string, sedangkan
`LoginController` menyimpan `$userSchool->school_id` yang sudah integer — tanpa cast, tipe isi
session berbeda tergantung dari mana ia ditulis.

### Rute

```php
Route::get('/select-school',      [SchoolSwitchController::class, 'index'])->name('select.school');
Route::get('/select-school/list', [SchoolSwitchController::class, 'list'])->name('select.school.list');
Route::post('/select-school',     [SchoolSwitchController::class, 'store']);
```

Ditempatkan di dalam grup `auth` tapi **di luar** grup `check.school.access`, persis seperti
`select.school` yang sudah ada. Ini penting: user yang belum punya sekolah aktif tetap harus bisa
memanggilnya.

## Helper `can_switch_school()`

```php
function can_switch_school(): bool
{
    $user = auth()->user();
    if (! $user) return false;

    return $user->hasRole('super_admin') || $user->userSchools()->count() > 1;
}
```

Kondisi ini sekarang tertulis inline di `navbar.blade.php:35`. Modal, tombol navbar, dan kartu
sidebar ketiganya harus sepakat — kalau tidak, user dengan satu sekolah mendapat tombol yang
memicu modal yang tidak pernah dirender. Satu helper menjaga ketiganya sinkron.

Untuk user dengan satu sekolah, kartu sidebar tetap tampil sebagai informasi tapi bukan tombol,
dan panah `sb-school-arrow` disembunyikan.

Kartu sidebar tetap berupa `<a>`, bukan diubah jadi `<button>`. Dua alasan: isinya `<div>`, yang
tidak sah di dalam `<button>`; dan `href` yang tetap menunjuk ke `/select-school` berfungsi sebagai
jalan mundur kalau JavaScript gagal dimuat. Saat user tidak berhak berpindah, atribut `href`-nya
tidak dirender sama sekali — `<a>` tanpa `href` memang tidak interaktif menurut spesifikasi HTML,
dan CSS `.sb-school-card:not([href])` menyesuaikan kursor serta hover-nya.

## Alur

```
Klik "Ganti Sekolah" (navbar) atau kartu sekolah (sidebar)
  → modal.classList.add('show')
  → fetch /select-school/list  →  inject ke #schoolSwitcherList
  → fokus ke kotak cari

Ketik di kotak cari
  → debounce 250ms  →  fetch ?q=...  →  ganti isi wadah

Klik kartu sekolah
  → isi hidden input school_id  →  form.submit()
  → POST /select-school  →  store()  →  redirect dashboard
```

Setiap fetch baru membatalkan yang sebelumnya lewat `AbortController`. Tanpa itu, mengetik cepat
bisa membuat respons lama tiba belakangan dan menimpa hasil yang lebih baru.

### Interaksi dengan `globalConfirmModal`

`layouts/app.blade.php:128` memasang listener `submit` di document yang menyergap semua form yang
tombol submit-nya bergaya `.btn-primary`, lalu memunculkan modal konfirmasi.

Form kita dikirim lewat `form.submit()`, yang menurut spesifikasi HTML tidak memicu event
`submit` — jadi secara teknis lolos dengan sendirinya. Tapi itu kebetulan, bukan jaminan.
Form tetap diberi atribut `data-no-confirm` supaya niatnya eksplisit dan tetap aman kalau suatu
saat diubah ke `requestSubmit()`, yang memang memicu event tersebut.

## Penanganan galat

| Keadaan | Tampilan |
|---|---|
| Memuat pertama kali | Placeholder redup di wadah daftar |
| Memuat saat mencari | Wadah diredupkan, isi lama dipertahankan |
| Fetch gagal / HTTP bukan 2xx | "Gagal memuat daftar sekolah" + tombol **Coba lagi**. Modal tetap terbuka |
| Pencarian nihil | "Sekolah tidak ditemukan" + ajakan mengubah kata kunci |
| Tidak punya sekolah | "Tidak ada sekolah yang bisa diakses" |
| Hasil pas `LIST_LIMIT` | Catatan kecil: "Ketik nama sekolah untuk mempersempit" |

Saat mencari, isi lama sengaja **tidak** dihapus sebelum respons tiba. Kalau dikosongkan, daftar
berkedip di setiap ketikan.

`AbortError` tidak boleh diperlakukan sebagai kegagalan. Kalau tidak difilter, setiap ketikan yang
membatalkan request sebelumnya memunculkan pesan "Gagal memuat" — padahal pembatalan itu kita
sendiri yang lakukan.

Setelah kartu diklik, seluruh daftar dinonaktifkan dan kartu yang diklik berganti jadi
"Memindahkan…". Tanpa ini, klik ganda pada koneksi lambat mengirim dua POST.

Modal ditutup lewat tombol ✕, klik backdrop, dan tombol Esc.

Respons 403 dari `store()` hanya bisa dicapai lewat manipulasi request, jadi halaman error bawaan
Laravel sudah memadai.

## Pengujian

`tests/Feature/SchoolSwitchTest.php`, memakai `RefreshDatabase` di atas sqlite in-memory yang sudah
dikonfigurasi di `phpunit.xml:26`.

1. `list()` hanya mengembalikan sekolah milik user; sekolah milik user lain tidak muncul
2. `list()` untuk super admin mengembalikan semua sekolah aktif
3. Sekolah nonaktif tidak muncul di daftar
4. `?q=` menyaring berdasarkan nama
5. `store()` dengan `school_id` yang bukan milik user → 403, session tidak berubah
6. `store()` dengan sekolah sah → session terisi, redirect ke dashboard
7. `store()` dengan sekolah nonaktif → 403

Nomor 5 dan 7 adalah inti perbaikan otorisasi. Keduanya sudah diverifikasi punya gigi: dengan
penjagaan di `store()` dinonaktifkan sementara, keduanya gagal dengan 302 — yaitu perpindahan
berhasil, persis celah yang diperbaiki.

### Dua hal yang harus disiapkan agar test bisa jalan

`APP_URL` di `.env` menunjuk ke subfolder XAMPP (`http://localhost/laravel-sms/public`). Saat diuji,
`route()` ikut memakai prefix itu sehingga tidak ada rute yang cocok dan semua request menjadi 404.
Karena itu `phpunit.xml` menyetel `APP_URL=http://localhost` khusus untuk lingkungan test.

Ekstensi `pdo_sqlite` dan `sqlite3` harus aktif di `php.ini`. Pada instalasi XAMPP bawaan keduanya
masih dikomentari meski DLL-nya tersedia di `php/ext/`. Ini konfigurasi lingkungan, di luar repo.

Proyek baru punya `UserFactory`, sedangkan test ini butuh `School` (wajib `school_type_id`, `name`,
dan `slug` yang unik), `SchoolType`, serta baris `user_schools` (kolom `role` bertipe smallInteger).
Karena itu `SchoolFactory` dan `SchoolTypeFactory` ikut dibuat — sekalian jadi fondasi untuk test
modul lain nanti.

Perilaku JavaScript tidak diuji otomatis. Proyek ini belum punya perkakas test JS sama sekali, dan
memasangnya demi satu modal jauh lebih mahal daripada manfaatnya. Verifikasinya manual: buka modal,
cari, pindah sekolah, lalu matikan jaringan dan buka lagi untuk melihat keadaan gagal.

## Di luar cakupan

**Verifikasi ulang `session('active_school_id')` di `CheckActiveSchool` pada setiap request.**
Setelah `store()` diperbaiki, semua jalan masuk penulisan session sudah aman — `LoginController`
memang hanya membaca relasi milik user sendiri. Menambah verifikasi per-request berarti satu query
di setiap halaman untuk menutup lubang yang pintunya sudah ditutup.

**Mengubah halaman `/select-school`.** Tetap radio + tombol "Lanjutkan" seperti sekarang. Halaman
itu hanya dilewati sekali setelah login dan tidak bermasalah.

**Menyatukan markup halaman dan modal.** Keduanya memang mirip, tapi mode interaksinya berbeda
(radio versus klik-langsung). Menyatukannya butuh flag mode di partial bersama — kompleksitas yang
tidak sepadan untuk halaman sepanjang 40 baris yang jarang dibuka.
