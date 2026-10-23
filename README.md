# Velocity RentCar

Website rental mobil untuk daerah Bogor dan sekitarnya, dibangun dari desain UI/UX
"Velocity RentCar" (Project Akhir Mata Kuliah UI/UX Design, Universitas Bina Sarana Informatika 2025).

Dibuat dengan **Laravel 10**, **Blade**, **Tailwind CSS**, **Alpine.js**, dan **MySQL**.

## Fitur

Mengikuti User Flow dan hasil Ideate di makalah.

**Pengguna**
- Start page, Sign in (email atau nomor telepon, "Ingatkan Saya"), Buat Akun, Sign in with Google
- Home: lokasi (Bogor Barat, dll.), notifikasi, Search + filter (transmisi, bahan bakar, jenis, kapasitas), banner, Populer Cars
- Explore per merek (Toyota, Honda, Hyundai, Suzuki) dengan tombol "Lihat Detail"
- Detail mobil: geser foto (bisa jadi tampilan 360° jika admin mengunggah foto dari berbagai sudut), Hubungi Penyewa (Telpon/Chat), Specs, Deskripsi, ulasan, Book Now
- Booking: Rental Summary dengan estimasi biaya realtime, kode promo, lalu 4 langkah
  (Informasi Penyewa, Jadwal Sewa + kalender, Metode Pembayaran, Konfirmasi), Rental Now / Batalkan
- Info Bayar "Pembayaran Sukses", Kembali ke Menu, Download bukti pembayaran (PDF)
- Saved (favorit), Chat Customer Service (live chat), Notification, Profile, Riwayat Pemesanan + Pesan Lagi (repeat order), penilaian setelah sewa selesai
- Tombol "Butuh Bantuan" di setiap halaman

**Admin** (tampilan dan fitur dipisah dari pengguna biasa). Semua isi toko bisa diubah dari panel admin:

| Menu | Yang bisa diatur |
|------|------------------|
| Dashboard | Pendapatan, pesanan aktif, grafik 6 bulan, pengambilan hari ini |
| Pesanan | Daftar & detail pesanan, ubah status + catatan (pelanggan otomatis dapat notifikasi) |
| Chat CS | Membalas chat pelanggan |
| Mobil | Tambah/edit/hapus mobil, foto (bisa banyak), harga, spesifikasi, populer, ketersediaan |
| Merek | Nama, logo, urutan merek di Explore |
| Kategori | Jenis mobil (MPV, SUV, ...) untuk kartu dan filter |
| Kode Promo | Kode, persen diskon, maksimal diskon, masa berlaku, aktif/nonaktif |
| Metode Pembayaran | Pilihan pembayaran (Kartu Debit / E-Wallet), urutan, aktif/nonaktif |
| Lokasi Layanan | Wilayah untuk "Location" dan lokasi pengambilan |
| Pelanggan & Admin | Tambah/edit/hapus akun pelanggan dan admin, reset password |
| Ulasan | Lihat & hapus ulasan (rating mobil ikut berubah) |
| Pengumuman | Kirim info/promo ke menu Notification semua pelanggan |
| Pengaturan Toko | Nama & tagline toko, nomor CS/WhatsApp, judul & teks halaman Start/Sign in/Buat Akun, foto Start & Sign in, banner Home |

Data yang sudah dipakai (mis. kategori yang masih punya mobil) tidak bisa dihapus agar riwayat tetap utuh; cukup dinonaktifkan.

> Pembayaran masih **simulasi**: seperti di prototipe, setelah "Rental Now" pesanan langsung
> berstatus "Sudah Dibayar". Integrasi payment gateway (mis. Midtrans) bisa ditambahkan nanti.

## Struktur database

Semua tabel memakai **UUID** sebagai primary key (tanpa auto increment). Tabel `favorites`
memakai kunci gabungan `user_id + car_id`, dan `settings` memakai nama pengaturan sebagai kunci. Tidak ada kolom yang menyimpan data ganda:

| Tabel | Isi | Nilai yang dihitung, tidak disimpan |
|-------|-----|-------------------------------------|
| `users` | akun, peran (user/admin), data penyewa (nama, telepon, alamat, lokasi) | — |
| `locations` | wilayah layanan | — |
| `categories` | jenis mobil | — |
| `payment_methods` | metode pembayaran | — |
| `settings` | pengaturan toko (kunci = nama pengaturan) | — |
| `brands` | merek dan logo | — |
| `cars` | data mobil | rating (rata-rata ulasan di `bookings`) |
| `car_photos` | foto mobil berurutan; foto pertama menjadi foto utama | — |
| `promos` | kode promo | — |
| `bookings` | pesanan: mobil, tanggal, harga sewa yang disepakati, diskon, pembayaran, status, ulasan | durasi (dari tanggal) dan total harga |
| `favorites` | mobil yang disimpan (menu Saved) | — |
| `messages` | chat pelanggan dengan Customer Service | — |
| `user_notifications` | notifikasi pelanggan | — |

## Menjalankan di Laragon

1. Jalankan Laragon (Apache/MySQL), lalu buat database `velocity_rentcar`.
2. Di folder project:

   ```bash
   composer install
   npm install
   npm run build
   php artisan migrate:fresh --seed
   php artisan storage:link
   php artisan serve
   ```

3. Buka http://127.0.0.1:8000

## Akun demo

Akun dibuat oleh `database/seeders/DatabaseSeeder.php` (password semuanya: `password`). Login bisa memakai email atau nomor telepon.

| Peran | Email |
|-------|-------|
| Admin | admin@velocity.test |
| User  | brot@velocity.test |

Selain itu ada 2 pelanggan contoh lain: `rina@velocity.test` dan `dimas@velocity.test`.

**Masuk tanpa mengetik:** halaman Sign in punya kotak **Akun Demo** dengan tombol *Demo Pengguna* (masuk sebagai Brot)
dan *Demo Admin*. Tombol ini aktif jika `DEMO_LOGIN=true` di `.env`. **Matikan (`DEMO_LOGIN=false`) sebelum website
dipakai sungguhan**, karena siapa pun bisa masuk sebagai admin lewat tombol itu.

Jika akun demo hilang (misalnya database terhapus), buat ulang dengan `php artisan migrate:fresh --seed`.

Kode promo contoh: `VELOCITY10` dan `BOGORHEMAT`.

## Konfigurasi tambahan (`.env`)

- `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`: isi dari Google Cloud Console agar tombol
  "Sign in with Google" aktif. Redirect URI: `http://127.0.0.1:8000/auth/google/callback`.

## Catatan aset

Semua foto mobil (HD, lebar 1920px), foto halaman Sign in, dan logo merek (SVG) berasal dari Wikimedia Commons
dengan lisensi CC BY-SA 4.0 / CC0 / Public domain. Daftar pembuat dan sumbernya ada di `public/images/CREDITS.md`.
Foto bisa ditambah atau diganti lewat menu **Admin, lalu Mobil, lalu Edit**.
