# Penjelasan Role Admin dan User

## Admin
- Dapat mengakses fitur administrasi flight melalui endpoint API.
- Dapat melihat dan mengelola semua booking.
- Dapat mengakses route web admin via middleware `EnsureUserRole:admin`.

## User
- Dapat melakukan booking melalui endpoint API.
- Hanya dapat mengakses booking miliknya sendiri.
- Dapat melihat history booking dan menggunakan fitur user.

## Implementasi
- `role` disimpan pada kolom `users.role`.
- Middleware `role` memeriksa nilai role.
- Policy `BookingPolicy` dan `FlightPolicy` memastikan authorization berdasar role dan kepemilikan data.
