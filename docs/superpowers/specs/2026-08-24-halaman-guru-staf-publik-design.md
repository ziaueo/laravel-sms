# Halaman Guru & Staf di Website Publik

Tanggal: 2026-08-24

## Ringkasan

Halaman publik baru di `/s/{slug}/guru` yang menampilkan susunan kepala sekolah, guru, dan tenaga
kependidikan sebagai direktori berkartu — kepala sekolah menonjol di atas, sisanya dikelompokkan
per kategori jabatan.

Sekalian membetulkan data jabatan: kolom `positions.type` selama ini tidak pernah dipakai, dan
"Kepala Sekolah" tercatat sebagai Staff.

## Masalah

Data guru dan staf sudah lengkap di sistem (`teachers` dengan `position_id`, `photo`, `nip`,
`is_active`), tapi sama sekali tidak muncul di website sekolah. Pengunjung — terutama orang tua
calon siswa — tidak punya cara melihat siapa yang mengajar di sana.

### Kendala data

`positions` hanya punya `id`, `name`, `type`, dan `timestamps`. Tabel ini global, tidak punya
`school_id`, dan **tidak punya kolom urutan maupun relasi atasan-bawahan**. Karena itu bagan
organisasi bercabang tidak mungkin dibuat dari data yang ada.

Lebih jauh, `PositionSeeder.php:17` menyimpan "Kepala Sekolah" dengan `type = STAFF`. Kalau
dikelompokkan apa adanya, kepala sekolah akan muncul berjajar dengan Satpam dan Penjaga Sekolah.

Penelusuran memastikan kolom `type` **tidak dipakai di satu tempat pun** — baik controller maupun
view hanya menyentuh `position_id`. Accessor `getTypeLabelAttribute()` dan `PositionConstant::getAll()`
juga tidak pernah dipanggil. Jadi mengubah arti kolom itu aman.

## Keputusan

| Hal | Keputusan |
|---|---|
| Bentuk | Direktori berkartu berkelompok, bukan bagan bergaris |
| Lokasi | Halaman sendiri `/s/{slug}/guru`, plus menu "Guru & Staf" di navbar publik |
| Siapa tampil | Semua pegawai dengan `is_active = true`, otomatis |
| Isi kartu | Foto, nama, jabatan, NIP |
| Sumber urutan | Kolom `order` baru di `positions` |
| Kategori | `positions.type`, ditambah nilai `PIMPINAN` |

### Kenapa bukan bagan bergaris

Bagan pohon butuh relasi induk-anak antar jabatan plus antarmuka admin untuk mengaturnya, dan tetap
sulit dibuat terbaca di layar ponsel. Direktori berkelompok menyampaikan informasi yang sama —
siapa memimpin, siapa mengajar, siapa mendukung — dengan satu kolom tambahan.

### Kenapa tidak menampilkan telepon, email, alamat, tanggal lahir

Semuanya ada di tabel `teachers`, tapi tidak satu pun berguna bagi pengunjung website dan semuanya
data pribadi. Yang tampil hanya nama, jabatan, foto, dan NIP.

### Kenapa inisial, bukan foto bawaan

`Teacher::getPhotoUrlAttribute()` menunjuk ke `public/assets/images/default/avatar.png`, dan
**folder itu tidak ada di repo**. Accessor tersebut tidak dipakai di mana pun sehingga belum pernah
menimbulkan masalah, tapi memakainya di sini akan menghasilkan grid penuh gambar rusak.

Halaman ini memakai `Teacher::getInitialsAttribute()` yang sudah tersedia: guru tanpa foto tampil
sebagai lingkaran berinisial, pola yang sama dengan avatar di navbar aplikasi. Tidak ada berkas
gambar baru yang perlu ditambahkan.

Memperbaiki `getPhotoUrlAttribute()` dan `School::getLogoUrlAttribute()` berada di luar cakupan —
keduanya tidak dipanggil dari mana pun, jadi tidak ada yang rusak hari ini.

## Perubahan data

Satu migrasi menambah kolom dan sekalian membetulkan sepuluh baris yang sudah ada:

```php
$table->smallInteger('order')->default(0)->after('type');
```

| Jabatan | type | order |
|---|---|---|
| Kepala Sekolah | PIMPINAN | 1 |
| Wakil Kepala Sekolah | PIMPINAN | 2 |
| Guru Kelas | GURU | 10 |
| Guru Mata Pelajaran | GURU | 11 |
| Guru BK | GURU | 12 |
| Staff TU | STAFF | 20 |
| Bendahara | STAFF | 21 |
| Pustakawan | STAFF | 22 |
| Satpam | STAFF | 23 |
| Penjaga Sekolah | STAFF | 24 |

Angka diberi jarak supaya jabatan baru bisa disisipkan tanpa menomori ulang semuanya.

Perbaikan data dikerjakan di migrasi, bukan di seeder, karena `PositionSeeder` memakai
`firstOrCreate` yang tidak menyentuh baris yang sudah ada. Seeder tetap diperbarui agar instalasi
baru langsung benar.

`PositionConstant` mendapat `PIMPINAN = 3` beserta labelnya.

Nama kolom `order` memang kata kunci SQL, tapi query builder Laravel mengutipnya otomatis dan
proyek ini sudah memakai nama yang sama di `banners.order` dan `school_types.order`.

## Berkas

**Baru**

| Berkas | Tanggung jawab |
|---|---|
| `database/migrations/*_add_order_to_positions_table.php` | Kolom `order` + perbaikan sepuluh baris jabatan |
| `resources/views/public/guru.blade.php` | Halaman beserta gayanya lewat `@push('styles')` |
| `database/factories/TeacherFactory.php` | Prasyarat test |
| `database/factories/PositionFactory.php` | Prasyarat test |
| `tests/Feature/PublicStaffPageTest.php` | Uji penyaringan, pengelompokan, dan privasi |

**Diubah**

| Berkas | Perubahan |
|---|---|
| `app/Constants/PositionConstant.php` | Tambah `PIMPINAN` |
| `app/Models/Position.php` | `order` masuk `$fillable`, tambah `HasFactory` |
| `app/Models/Teacher.php` | Tambah `HasFactory` |
| `database/seeders/PositionSeeder.php` | Sertakan `order`, pindahkan pimpinan ke kategori baru |
| `app/Http/Controllers/Web/Public/PublicController.php` | Tambah `guru()` |
| `routes/web.php` | Tambah `GET /s/{slug}/guru` |
| `resources/views/layouts/public.blade.php` | Menu navbar + tautan footer |

## Controller

```php
$teachers = Teacher::where('school_id', $school->id)
    ->where('is_active', true)
    ->with('position')
    ->get()
    // Kunci gabungan: urutan jabatan dulu, lalu nama. Digabung jadi satu string
    // supaya hasilnya tidak bergantung pada kestabilan pengurutan PHP.
    ->sortBy(fn ($t) => sprintf('%03d|%s', $t->position?->order ?? 999, $t->full_name))
    ->values();

$byType     = $teachers->groupBy(fn ($t) => $t->position?->type ?? 0);
$leadership = $byType->get(PositionConstant::PIMPINAN, collect());
$headmaster = $leadership->first();
```

`$headmaster` adalah orang di kelompok Pimpinan dengan `order` terkecil — bukan hasil pencocokan
nama jabatan, sehingga tidak patah kalau penamaannya berubah.

Sisanya disusun jadi daftar seksi yang tinggal diloop view:

| Seksi | Isi |
|---|---|
| Pimpinan Sekolah | Kelompok PIMPINAN tanpa orang pertama (sudah tampil di atas) |
| Guru | Kelompok GURU |
| Tenaga Kependidikan | Kelompok STAFF |
| Lainnya | `position_id` kosong |

Seksi kosong tidak dirender. Seksi "Lainnya" ada supaya pegawai yang jabatannya belum diisi tetap
muncul — kolom `teachers.position_id` memang nullable.

Satu query dengan `with('position')`; pengelompokan dan pengurutan dikerjakan di PHP karena jumlah
pegawai per sekolah terbatas pada puluhan.

## Tampilan

Halaman memakai `layouts.public` yang sudah ada beserta kelas `.hero`, `.sec`, `.sec-title`,
`.container`, dan `.pcard`. Gaya khusus kartu orang didorong lewat `@push('styles')` supaya menempel
pada halamannya sendiri, bukan menggemukkan `<style>` di layout yang dipakai semua halaman publik.

Grid kartu 4 kolom di desktop, 3 kolom di bawah 900px, 2 kolom di bawah 640px — lebih rapat daripada
`.grid-3` bawaan karena kartu orang lebih sempit daripada kartu berita.

Kalau sekolah belum punya pegawai aktif sama sekali, halaman menampilkan pesan bahwa datanya belum
tersedia, bukan halaman kosong.

## Pengujian

`tests/Feature/PublicStaffPageTest.php`, `RefreshDatabase` di atas sqlite in-memory:

1. Halaman terbuka dan memuat nama pegawai aktif
2. Pegawai nonaktif tidak muncul
3. Pegawai sekolah lain tidak muncul
4. Kepala sekolah muncul di blok utama, bukan di daftar kelompok
5. Pegawai tanpa jabatan tetap muncul
6. NIP ditampilkan
7. Telepon, email, dan alamat **tidak** ikut terbawa ke halaman
8. Sekolah nonaktif menghasilkan 404

Nomor 7 menjaga keputusan privasi tetap berlaku kalau kartunya diubah nanti.

## Di luar cakupan

**Antarmuka admin untuk mengatur urutan jabatan.** Belum ada layar kelola jabatan sama sekali —
sepuluh baris itu datang dari seeder dan tidak bisa ditambah lewat aplikasi. Membuat layar
pengaturan urutan untuk daftar yang tidak bisa diubah tidak ada gunanya sekarang.

**Halaman detail per guru.** Kartu tidak bisa diklik. Belum ada permintaannya, dan menambah halaman
profil per guru memunculkan pertanyaan privasi baru yang belum dijawab.

**Memperbaiki accessor `photo_url` dan `logo_url`.** Keduanya menunjuk berkas yang tidak ada, tapi
tidak dipanggil dari mana pun.
