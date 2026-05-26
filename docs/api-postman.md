# Dokumentasi API Postman

## Prasyarat
1. Jalankan aplikasi Laravel.
2. Login melalui `POST /api/login`.
3. Simpan token pada variabel Postman `token`.

## Endpoint Utama

### Auth
- `POST /api/register`
- `POST /api/login`
- `POST /api/logout`

### Flight API
- `GET /api/flights`
- `GET /api/flights/{id}`
- `POST /api/flights`
- `PUT /api/flights/{id}`
- `DELETE /api/flights/{id}`

### Booking API
- `GET /api/bookings`
- `GET /api/bookings/{id}`
- `POST /api/bookings`
- `PUT /api/bookings/{id}`
- `DELETE /api/bookings/{id}`

## Contoh Request

### Login
```json
{
  "email": "admin@example.com",
  "password": "password"
}
```

### Create Flight
```json
{
  "kode_penerbangan": "SKY123",
  "maskapai": "SkyBook Air",
  "asal": "Jakarta",
  "tujuan": "Bali"
}
```

### Create Booking
```json
{
  "schedule_id": 1,
  "jumlah_tiket": 2
}
```

## Response Standar

```json
{
  "success": true,
  "message": "Booking berhasil dibuat",
  "data": {}
}
```

## Error Response

```json
{
  "success": false,
  "message": "Jumlah tiket melebihi kapasitas yang tersedia.",
  "data": null
}
```
