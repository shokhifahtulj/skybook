# ERD Database

```mermaid
erDiagram
    USER ||--o{ BOOKING : makes
    FLIGHT ||--o{ BOOKING : has
    FLIGHT ||--|| ROUTE : belongs_to
    FLIGHT ||--|| AIRCRAFT : belongs_to
    AIRCRAFT ||--o{ FLIGHT : has
    FLIGHT ||--o{ SCHEDULE : has
    SCHEDULE ||--o{ BOOKING : uses

    USER {
        bigint id
        string name
        string email
        string password
        string role
    }

    FLIGHT {
        uuid id
        string flight_number
        uuid airline_id
        uuid route_id
        uuid aircraft_id
    }

    ROUTE {
        uuid id
        uuid origin_airport_id
        uuid destination_airport_id
        int estimated_duration
    }

    AIRCRAFT {
        uuid id
        string model
        int capacity
    }

    SCHEDULE {
        bigint id
        uuid flight_id
        date tanggal
        time jam_berangkat
        time jam_tiba
        int kapasitas
    }

    BOOKING {
        uuid id
        bigint user_id
        bigint schedule_id
        uuid flight_id
        string booking_status
        string payment_status
        int jumlah_tiket
        decimal total_harga
        string pnr
    }
```

## Penjelasan Relasi
- `User` memiliki banyak `Booking`.
- `Booking` dimiliki oleh `User`, `Flight`, dan `Schedule`.
- `Flight` dimiliki oleh `Route` dan `Aircraft`.
- `Schedule` dimiliki oleh `Flight`.
- `Aircraft` memiliki banyak `Flight`.
