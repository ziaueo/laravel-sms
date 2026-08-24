# Lightbox Foto

Tanggal: 2026-08-24

## Ringkasan

Foto konten di situs publik maupun di dalam aplikasi bisa diklik untuk dilihat besar dalam sebuah
overlay, lengkap dengan navigasi antar foto sekelompok dan keterangannya.

## Masalah

Foto di seluruh proyek dirender kecil dan terpotong (`object-fit: cover` dengan tinggi tetap
130–160px). Tidak ada satu pun cara melihat versi utuhnya. Di halaman galeri publik
(`resources/views/public/galeri.blade.php:20`) foto bahkan sama sekali tidak bisa diklik —
album berisi belasan foto hanya bisa dilihat sebagai petak-petak kecil yang terpotong.

## Dua kendala yang membentuk desain

**Situs publik saat ini nol JavaScript.** `layouts/public.blade.php` hanya memuat `<style>` inline
dan `@stack('styles')` — tidak ada `@vite`, tidak ada `<script>`, bahkan tidak ada
`@stack('scripts')`. Lightbox akan menjadi JavaScript pertama di situs publik.

**Aplikasi dan situs publik memakai jalur aset yang berbeda.** Aplikasi lewat bundle Vite
(`resources/js/app.js`, `resources/css/app.css`); situs publik lewat `<style>` inline di layout.
Menulis lightbox di kedua tempat berarti dua salinan yang harus dijaga tetap sama.

## Keputusan

| Hal | Keputusan |
|---|---|
| Cakupan | Opt-in: foto konten ditandai satu per satu, bukan semua `<img>` |
| Penanda | Atribut `data-lightbox="<grup>"` pada `<img>` |
| Isi | Navigasi antar foto sekelompok, penghitung, dan keterangan |
| Penempatan kode | Satu partial mandiri berisi markup + `<style>` + `<script>` |

### Kenapa opt-in

Dari 25 berkas view yang memuat `<img>`, sebagian besar justru tidak pantas dizoom: logo sekolah di
navbar, logo aplikasi di halaman login dan pilih sekolah, avatar kecil di modal ganti sekolah, dan
pratinjau unggahan di form tambah/edit. Pendekatan "semua kecuali yang dikecualikan" membuat setiap
gambar baru ikut aktif tanpa ada yang memutuskan.

### Kenapa inline, bukan Vite

Layout publik tidak punya pipeline Vite sama sekali, jadi kode yang hanya hidup di bundle tidak akan
sampai ke sana. Partial mandiri yang membawa gaya dan skripnya sendiri bisa dipakai kedua layout
tanpa duplikasi.

Ini juga bukan pola baru di proyek: `layouts/app.blade.php:101` sudah memuat `<script>` inline
untuk `globalConfirmModal`.

## Penanda

```blade
<img src="..." alt="Upacara bendera" data-lightbox="galeri-3">
<img src="..." data-lightbox="staf" data-caption="Kepala Sekolah">
```

| Atribut | Arti |
|---|---|
| `data-lightbox` | Nama grup. Gambar dengan nilai sama bisa digeser bolak-balik |
| `data-caption` | Keterangan opsional. Kalau kosong, dipakai `alt` |

Grup ditentukan saat gambar diklik, dengan mengumpulkan seluruh `[data-lightbox="<nilai sama>"]`
di dokumen sesuai urutan DOM. Tidak ada daftar yang perlu didaftarkan lebih dulu, sehingga halaman
yang menampilkan beberapa album sekaligus otomatis punya grup terpisah per album.

## Perilaku

- Klik gambar bertanda membuka overlay pada gambar tersebut
- Tombol ‹ dan › serta panah kiri/kanan keyboard berpindah dalam grup, memutar di ujung
- Esc, klik latar, atau tombol ✕ menutup
- Penghitung "3 / 12" dan keterangan tampil di bawah gambar
- Tombol navigasi dan penghitung disembunyikan kalau grup hanya berisi satu gambar
- Gambar bertanda mendapat `cursor: zoom-in`
- Body dikunci dari menggulir selama overlay terbuka, lalu dikembalikan
- Fokus pindah ke tombol tutup saat dibuka, dan kembali ke gambar asal saat ditutup

Gambar ditampilkan pada ukuran aslinya dibatasi `max-width: 92vw` dan `max-height: 82vh`. Proyek ini
menyimpan satu berkas per gambar tanpa versi thumbnail terpisah, jadi tidak diperlukan atribut untuk
menunjuk versi besar.

## Lokasi yang ditandai

**Situs publik**

| Berkas | Gambar | Grup |
|---|---|---|
| `public/galeri.blade.php` | Foto album | `galeri-{id album}` |
| `public/galeri.blade.php` | Cover album (saat album kosong) | `galeri-{id album}` |
| `public/berita-detail.blade.php` | Cover berita | `berita` |
| `public/guru.blade.php` | Foto kepala sekolah dan tiap pegawai | `staf` |

**Aplikasi**

| Berkas | Gambar | Grup |
|---|---|---|
| `school/cms/gallery-show.blade.php` | Foto album | `galeri-item` |
| `school/cms/banners.blade.php` | Pratinjau banner | `banner` |
| `school/teachers/show.blade.php` | Foto guru | `foto` |
| `school/students/show.blade.php` | Foto siswa | `foto` |
| `school/teachers/index.blade.php` | Avatar kartu guru | `guru` |
| `school/students/index.blade.php` | Avatar siswa | `siswa` |

Sudah dipastikan tidak satu pun dari sepuluh gambar ini berada di dalam `<a>`, sehingga tidak
bentrok dengan klik navigasi.

## Yang sengaja dilewati

**Logo dan avatar antarmuka** — navbar, sidebar, halaman login, registrasi, pilih sekolah, dan
modal ganti sekolah. Semuanya elemen navigasi, bukan konten.

**Pratinjau unggahan di form** tambah/edit guru, siswa, dan pos. Gambarnya bahkan belum tentu
tersimpan.

**Thumbnail berita dan galeri di beranda serta di daftar berita.** Semuanya berada di dalam tautan
menuju artikel atau albumnya. Klik di situ harus membuka halamannya, bukan memperbesar gambar.

**Cover album di `school/cms/galleries.blade.php`.** Punya gambar cadangan dari `placehold.co` saat
album belum bercover, dan petaknya berfungsi sebagai jalan masuk ke album.

## Pengujian

Perilaku JavaScript tidak diuji otomatis. Proyek belum punya perkakas test JS sama sekali, dan
memasangnya demi satu lightbox jauh lebih mahal daripada manfaatnya.

Yang diuji adalah bahwa penandanya benar-benar terpasang di HTML yang dirender: satu assertion
ditambahkan ke `tests/Feature/PublicStaffPageTest.php` yang sudah ada, pada guru yang punya foto.
Ini murah dan menjaga penanda tidak hilang diam-diam saat view diutak-atik nanti.

Sisanya verifikasi manual: buka galeri publik, klik foto, geser dengan panah keyboard, tutup dengan
Esc, dan pastikan latar tidak ikut menggulir saat overlay terbuka.

## Di luar cakupan

**Zoom dan geser di dalam overlay.** Cubit untuk memperbesar dan seret untuk menggeser butuh
penanganan sentuh yang jauh lebih rumit, dan foto sekolah umumnya sudah terbaca pada 82vh.

**Memuat versi beresolusi lebih tinggi.** Proyek menyimpan satu berkas per gambar; tidak ada versi
lain untuk dimuat.

**Animasi transisi antar foto.** Pergantian gambar langsung, tanpa efek geser.
