
# SkyBook - Sistem Booking Penerbangan

SkyBook adalah sistem booking penerbangan berbasis Laravel 12 yang mendukung autentikasi Breeze, role admin/user, booking tiket, pemilihan kursi, e-ticket printable PDF, histori booking, pembatalan, validasi kapasitas, notifikasi sederhana, dan API RESTful dengan Sanctum.

## Daftar Fitur

### User
- Login, register, dan logout
- Pencarian penerbangan
- Booking tiket
- Pemilihan kursi
- Cetak e-ticket PDF
- Riwayat booking
- Batalkan booking
- Notifikasi sederhana

### Admin
- CRUD penerbangan
- CRUD jadwal
- Kelola booking
- Kelola user (melalui panel admin / endpoint API yang tetap dapat digunakan sesuai kebutuhan)

### Umum
- Dynamic pricing
- PNR otomatis
- Kursi otomatis berkurang setelah booking
- QR code pada e-ticket
- Validasi bentrok/jadwal dan kapasitas
- Testing unit dan feature

## Alur utama

1. User login/register
2. User mencari penerbangan
3. User memilih jadwal dan kursi
4. Booking dibuat dan kapasitas dikurangi
5. User dapat mencetak e-ticket dan melihat histori booking
6. Admin dapat mengelola master data dan operasi booking

## Installation

### Prasyarat
- PHP 8.2+
- Composer
- Node.js + npm
- MySQL / database yang tersedia

### Langkah instalasi

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
php artisan migrate --seed
npm run build
php artisan serve
```

### Akun default

- `admin@gmail.com` / `123456`
- `user@gmail.com` / `123456`
- `admin@demo.com` / `Admin123!`
- `user@demo.com` / `User123!`

## Cara menjalankan aplikasi

### Mode development

```bash
php artisan serve
npm run dev
```

Aplikasi dapat diakses di `http://127.0.0.1:8000`.

## Struktur project

- `app/Http/Controllers` — controller web & API
- `app/Models` — model Eloquent
- `app/Policies` — policy untuk otorisasi
- `app/Services` — service domain seperti booking, inventory, tiket
- `database/migrations` — skema database
- `database/seeders` — seeder data awal
- `resources/views` — Blade UI
- `routes/web.php` — route web
- `routes/api.php` — route API Sanctum
- `tests` — unit dan feature test

## Endpoint API

### Auth
- `POST /api/register`
- `POST /api/login`
- `POST /api/logout`

### Flight
- `GET /api/flights`
- `POST /api/flights`
- `PUT /api/flights/{id}`
- `DELETE /api/flights/{id}`

### Booking
- `GET /api/bookings`
- `POST /api/bookings`
- `PUT /api/bookings/{id}`
- `DELETE /api/bookings/{id}`

### Response standar

```json
{
  "success": true,
  "message": "Data berhasil diambil",
  "data": {}
}
```

### Error response

```json
{
  "success": false,
  "message": "Email atau password salah",
  "data": null
}
```

## Dokumentasi

- ERD: `docs/erd.md`
- Flowchart: `docs/flowchart.md`
- Dokumentasi Postman: `docs/api-postman.md`
- Struktur folder: `docs/folder-structure.md`
- Login flow: `docs/login-flow.md`
- Role & permission: `docs/roles.md`

## Testing

Jalankan seluruh test suite:

```bash
php artisan test
```

## Deployment

### GitHub

Simpan repo menggunakan Git, lalu push ke remote GitHub Anda.

```bash
git remote add origin <URL_REPOSITORY>
git add .
git commit -m "Initial SkyBook release"
git push -u origin main
```

> Push ke GitHub tidak dapat dilakukan dari lingkungan ini tanpa kredensial remote yang tersedia.

## Screenshot aplikasi

- Halaman login telah diverifikasi secara visual melalui browser lokal.
- Jika dibutuhkan, screenshot dapat ditambahkan ke folder `docs/` atau ditautkan pada README setelah tersedia.

## Status validasi

- `php artisan test` telah dijalankan dan seluruh suite lulus.
- Login default berhasil diverifikasi melalui `php artisan db:seed` dan pemeriksaan `Auth::attempt()`.

