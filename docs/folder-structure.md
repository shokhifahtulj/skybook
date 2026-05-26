# Struktur Folder Proyek

```text
app/
├── Http/
│   ├── Controllers/
│   │   ├── API/           # Controller API utama
│   │   └── Auth/          # Breeze controllers
│   ├── Middleware/       # Middleware role
│   └── Requests/         # FormRequest
├── Models/               # Eloquent models
├── Policies/             # Policy authorization
├── Providers/            # Gate registration
├── Services/             # Service business logic
├── Events/               # Event domain
├── Listeners/            # Event listeners
config/                   # Konfigurasi Laravel
database/
├── factories/            # Factory data dummy
├── migrations/           # Skema database
├── seeders/              # Seeder data
resources/views/          # Blade UI
routes/
├── api.php               # Route API
├── web.php               # Route web
├── auth.php              # Breeze routes
docs/                    # Dokumentasi teknis
tests/
├── Feature/              # Feature test
├── Unit/                 # Unit test
README.md                 # Dokumentasi install
postman_collection.json   # Koleksi Postman
```
