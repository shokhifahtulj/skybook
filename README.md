
# SkyBook - Sistem Booking Penerbangan

[![Laravel Security CI](https://github.com/shokhifahtulj/skybook/actions/workflows/laravel.yml/badge.svg)](https://github.com/shokhifahtulj/skybook/actions/workflows/laravel.yml)

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

## Security Features

- Laravel Sanctum untuk autentikasi token API
- Proteksi middleware `auth:sanctum` dan `throttle:api`
- Route protection untuk endpoint admin dan user
- RBAC berbasis Spatie Permission
- Validasi input dan exception handling terstruktur
- Endpoint operasional dan sensitif dilindungi
- CI/CD sederhana untuk audit dependensi, lint PHP, dan test

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

### GitHub & CI/CD

Repository ini dilengkapi dengan workflow GitHub Actions untuk menjalankan test dan langkah optimisasi. Workflow berada di `.github/workflows/laravel.yml`.

Contoh langkah push ke GitHub:

```bash
git remote add origin <URL_REPOSITORY>
git add .
git commit -m "Initial SkyBook release"
git push -u origin main
```

CI menjalankan `composer install`, `php artisan migrate`, `php artisan test`, dan caching (`config:cache`, `route:cache`, `view:cache`).

### Deployment Guide

1. Siapkan server (VPS/Platform) dengan PHP 8.2+, Composer, dan database.
2. Clone repo dan set environment variables di `.env` (lihat bagian `Environment Variables` di bawah).
3. Jalankan:

```bash
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

4. Siapkan queue worker (`php artisan queue:work`) jika ingin mengaktifkan pengiriman email asinkron.

### Environment Variables (penting)

Pastikan variabel berikut tersedia di `.env`:

- `APP_URL`
- `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME`
- `QUEUE_CONNECTION`


## Screenshot aplikasi

- Halaman login telah diverifikasi secara visual melalui browser lokal.
- Jika dibutuhkan, screenshot dapat ditambahkan ke folder `docs/` atau ditautkan pada README setelah tersedia.

## Status validasi

- `php artisan test` telah dijalankan dan seluruh suite lulus.
- Login default berhasil diverifikasi melalui `php artisan db:seed` dan pemeriksaan `Auth::attempt()`.

