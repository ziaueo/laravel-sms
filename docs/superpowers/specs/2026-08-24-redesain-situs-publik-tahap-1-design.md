# Redesain Situs Publik — Tahap 1

Tanggal: 2026-08-24

## Ringkasan

Menata ulang tampilan situs publik sekolah agar elegan dan modern tanpa kehilangan kesan formal,
sekaligus memperbaiki dua hal yang memang rusak: navigasi yang hilang di ponsel, dan banner CMS yang
tidak pernah dirender.

Ini tahap pertama dari perombakan bertahap. Tahap ini sengaja dibatasi pada hal yang **tidak menuntut
data baru** — semua seksi barunya hidup dari data yang sudah ada di sistem.

## Masalah

**Navigasi hilang total di ponsel.** `layouts/public.blade.php:54` menyembunyikan `.pnav-links` di
bawah 820px tanpa hamburger atau pengganti apa pun. Pengunjung dari ponsel hanya bisa mengklik logo.
Ini bukan kekurangan estetika — orang tua calon siswa hampir selalu membuka situs sekolah dari ponsel.

**Banner CMS tidak pernah muncul.** `PublicController::home()` mengambil `$banners`, tapi
`home.blade.php` tidak pernah merendernya. Modul Banner lengkap — admin bisa unggah, publish, dan
mengatur urutan — dan hasilnya tidak pernah dilihat siapa pun.

**Situs dan aplikasi terasa dua produk berbeda.** Situs publik memakai `#1a7a3c`, Segoe UI, tanpa
bayangan sama sekali. Aplikasi memakai `#2d6a4f`, Plus Jakarta Sans, dan sistem bayangan bertingkat.
Hijaunya berbeda tipis tapi cukup untuk terasa janggal saat berpindah.

**Beranda tipis.** Isinya hero gradien polos, berita, galeri, dan ajakan PPDB. Data yang sudah ada di
sistem — jumlah siswa, guru, kelas, ekstrakurikuler, visi, misi — tidak dipakai sama sekali.

## Cakupan tahap ini

| Termasuk | Tidak termasuk (tahap berikutnya) |
|---|---|
| Sistem desain publik | Sambutan Kepala Sekolah |
| Menu mobile | Testimoni (CRUD dari nol) |
| Hero slider dari banner CMS | FAQ (CRUD dari nol) |
| Seksi statistik, visi-misi, ekstrakurikuler | Modul Fasilitas (tabel baru) |
| Penataan ulang kesembilan halaman publik | |

Pemisahan ini bukan sekadar rapi-rapi: kolom sisi kanan semuanya menuntut form admin baru atau tabel
baru, sedangkan kolom kiri hidup dari data yang kemungkinan besar sudah terisi hari ini.

## Sistem desain

`resources/css/public.css` menjadi entry, mengimpor `variables.css` milik aplikasi lalu lima berkas di
`resources/css/public/`:

| Berkas | Isi |
|---|---|
| `nav.css` | Navbar, hamburger, panel menu mobile |
| `hero.css` | Slider banner dan hero cadangan |
| `cards.css` | Kartu berita, album, orang, ekstrakurikuler |
| `sections.css` | Kerangka seksi, judul, statistik, visi-misi |
| `footer.css` | Footer |

### Kenapa mengimpor token aplikasi

Dengan memakai `variables.css` yang sama, situs dan aplikasi tidak bisa lagi berbeda warna diam-diam.
Kalau palet aplikasi berubah, situs ikut berubah. Ini memang mengikat keduanya — dan itulah tujuannya,
karena masalah yang diperbaiki justru keduanya sudah terlanjur berbeda.

Blok `[data-theme="dark"]` ikut terbawa tapi tidak pernah aktif: atribut itu dipasang oleh skrip di
`layouts/app.blade.php`, dan layout publik tidak punya pengalih tema.

### Kenapa pindah dari `<style>` inline ke Vite

Setelah ditata ulang, gaya situs publik tumbuh dari sekitar 50 baris menjadi beberapa ratus. Menaruh
itu di dalam `<style>` layout membuat berkas layout tidak terbaca lagi.

Ini tidak menambah ketergantungan baru: `/public/build` ada di `.gitignore` dan `layouts/app.blade.php`
sudah memakai `@vite`, jadi `npm run build` memang sudah wajib saat deploy.

`vite.config.js` mendapat dua input baru: `resources/css/public.css` dan `resources/js/public.js`.

### Lightbox tetap partial dengan gaya inline

Sekarang layout publik punya Vite, sehingga `components/lightbox.blade.php` yang membawa `<style>` dan
`<script>` sendiri terlihat tidak konsisten. Itu disengaja: lightbox dipakai layout aplikasi **dan**
layout publik, yang memuat bundle berbeda. Memindahkannya ke bundle berarti menyalin kode yang sama ke
dua tempat — persis yang dihindari partial itu.

## Navigasi

Hamburger muncul di bawah 900px dan membuka panel menu. Sebelumnya tidak ada apa pun di sana.

- Navbar mendapat bayangan tipis begitu halaman digulir
- Menu aktif ditandai garis bawah, bukan kotak berlatar
- Panel mobile menutup saat salah satu menu diklik, saat latar diklik, dan saat Esc ditekan
- Fokus pindah ke menu pertama saat panel dibuka

## Beranda

Urutan seksi:

```
slider banner  →  statistik  →  visi & misi  →  berita
               →  ekstrakurikuler  →  galeri  →  ajakan PPDB
```

### Slider banner

Merender `$banners` yang selama ini terbuang. Tiap slide membawa `title`, `subtitle`, `image`,
`button_text`, dan `button_url` miliknya sendiri, urut menurut kolom `order`.

- Berganti tiap 6 detik, berhenti saat kursor berada di atasnya
- Titik penanda bisa diklik
- Tidak berganti sendiri kalau perangkat menyalakan `prefers-reduced-motion`
- Gradien gelap dipasang di atas foto supaya teksnya tetap terbaca apa pun fotonya

Kalau sekolah belum punya banner, hero jatuh ke gradien bertagline dengan dua tombol — sama seperti
sekarang. Sekolah baru tidak akan melihat kotak kosong.

### Statistik

Empat angka, semuanya dihitung dari data yang sudah ada:

| Angka | Sumber |
|---|---|
| Siswa | `students.status = AKTIF` |
| Guru | `teachers.is_active = true` |
| Kelas | `classrooms` pada **tahun ajaran aktif** sekolah |
| Ekstrakurikuler | `extracurriculars.is_active = true` |

Kelas wajib dibatasi ke tahun ajaran aktif. Tabel `classrooms` terikat `school_year_id`, sehingga
menghitung semua baris aktif akan menjumlahkan kelas dari tahun-tahun sebelumnya dan menghasilkan angka
yang terus menggelembung tiap tahun.

Seksi ini tidak dirender kalau keempat angkanya nol — sekolah yang baru dibuat tidak perlu memamerkan
deretan angka 0.

### Visi & Misi

Dari `school_profiles.vision` dan `mission`. Keduanya kolom teks bebas; `mission` dirender sebagai
daftar dengan memecah per baris. Seksi disembunyikan kalau keduanya kosong.

### Ekstrakurikuler

`extracurriculars` yang `is_active`, menampilkan nama dan deskripsi. Modul ini sudah punya CRUD lengkap,
jadi datanya kemungkinan besar sudah terisi. Disembunyikan kalau kosong.

## Halaman lain

Delapan halaman sisanya — profil, guru, berita, berita-detail, galeri, galeri-detail, kontak, ppdb —
ditata ulang memakai komponen yang sama: kartu berbayang halus, jarak lebih lega, dan Plus Jakarta Sans.

Lebar teks artikel di `berita-detail` dibatasi sekitar 70 karakter per baris. Teks selebar 1100px
melelahkan untuk dibaca panjang-panjang.

Struktur dan data kedelapan halaman itu tidak diubah — hanya tampilannya.

## Pengujian

`tests/Feature/PublicHomePageTest.php`:

1. Slider merender banner yang dipublikasikan, urut menurut `order`
2. Banner yang belum dipublikasikan tidak muncul
3. Tanpa banner, hero jatuh ke gradien bertagline
4. Angka statistik benar untuk siswa, guru, kelas, dan ekstrakurikuler
5. **Kelas hanya dihitung dari tahun ajaran aktif** — kelas tahun lalu tidak ikut
6. Seksi statistik tidak muncul saat semua angkanya nol
7. Seksi visi-misi muncul saat terisi, hilang saat kosong
8. Ekstrakurikuler aktif muncul, yang nonaktif tidak
9. Markup menu mobile hadir di halaman

Nomor 5 menjaga bug yang paling mudah lolos: angkanya terlihat wajar di tahun pertama dan baru salah
setelah tahun ajaran berganti.

### Risiko pada test yang sudah ada

`PublicGalleryPageTest` dan `PublicStaffPageTest` menguji markup yang akan berubah — antara lain kelas
CSS `lead-name` dan teks "Data belum tersedia". Keduanya disesuaikan agar menguji perilaku, bukan gaya.
Menyesuaikan test karena tampilannya sengaja diubah itu wajar; yang tidak boleh adalah melonggarkan
assertion sampai tidak lagi menguji apa pun.

Perilaku JavaScript — slider dan menu mobile — tidak diuji otomatis, karena proyek belum punya perkakas
test JS. Yang diuji adalah markup dan datanya hadir; sisanya verifikasi manual.

## Di luar cakupan

**Mode gelap untuk situs publik.** Token gelap ikut terbawa dari `variables.css` tapi tidak diaktifkan.
Situs sekolah dibaca sekali-sekali oleh orang luar, bukan dipelototi berjam-jam.

**Mengganti `placehold.co` sebagai gambar cadangan.** Masih dipakai di beberapa tempat dan berarti situs
memanggil layanan luar. Layak diganti, tapi bukan urusan tahap ini.

**Memperbaiki accessor `photo_url` dan `logo_url`** yang menunjuk berkas tidak ada. Masih tidak dipanggil
dari mana pun.
