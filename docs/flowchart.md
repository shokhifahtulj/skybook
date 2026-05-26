# Flowchart Sistem

```mermaid
flowchart TD
    A[Start] --> B[Login / Register]
    B --> C{Role?}
    C -->|admin| D[Dashboard Admin]
    C -->|user| E[Dashboard User]
    D --> F[Manage Flight]
    D --> G[Manage Schedule]
    F --> H[Create / Update / Delete Flight API]
    G --> I[Create / Update / Delete Schedule]
    E --> J[Browse Flight]
    J --> K[Create Booking]
    K --> L[Validate Capacity]
    L -->|ok| M[Persist Booking]
    L -->|fail| N[Return Validation Error]
    M --> O[Booking Success Response]
    O --> P[User History]
```

## Alur Login
1. User masuk melalui `/login` atau `/api/login`.
2. Sistem memvalidasi email dan password.
3. `Sanctum` mengeluarkan token.
4. Token digunakan pada endpoint API yang dilindungi.
