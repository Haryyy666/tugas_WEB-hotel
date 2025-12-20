# Sistem Informasi Reservasi Kamar Hotel (Native PHP + Bootstrap)

Generated: 2025-12-11T12:19:46.002200 UTC

## Isi project
- config/koneksi.php       -> koneksi database (sesuaikan credential)
- inc/auth_check.php       -> helper cek session & role
- auth/                    -> login, register (admin-only), logout, forgot, reset
- kamar/                   -> CRUD kamar
- tipe_kamar/              -> CRUD tipe kamar
- tamu/                    -> CRUD tamu
- reservasi/               -> CRUD reservasi (kalkulasi total_harga berdasarkan tipe_kamar.harga * lama_inap)
- assets/                  -> css/js (Bootstrap via CDN)
- sql/hotel_db.sql         -> file SQL untuk import struktur tabel
- index.php                -> redirect ke dashboard
- dashboard.php            -> ringkasan sederhana (counts)

## Cara pakai
1. Import file `sql/hotel_db.sql` ke MySQL (database `hotel_db`).
2. Sesuaikan `config/koneksi.php` jika perlu.
3. Jalankan di localhost (misal XAMPP) pada folder project.

Catatan: fitur email reset hanya disimulasikan (token ditampilkan). Untuk production, hubungkan fungsi email.
