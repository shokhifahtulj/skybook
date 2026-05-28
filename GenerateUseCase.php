```php
<?php

echo "=== SKYBOOK USE CASE GENERATOR ===\n\n";

$usecase = <<<EOT
flowchart TD

A([Start]) --> B[Login]
B --> C{Role?}

C -->|User| D[Search Flight]
D --> E[Choose Schedule]
E --> F[Choose Seat]
F --> G[Payment]
G --> H[Generate Ticket]

C -->|Admin| I[Manage Flight]
I --> J[Manage Schedule]
J --> K[Manage Booking]
K --> L[View Reports]

H --> M([Finish])
L --> M
EOT;

file_put_contents("usecase.mmd", $usecase);

echo "usecase.mmd berhasil dibuat!\n";
echo "Sekarang jalankan:\n";
echo "mmdc -i usecase.mmd -o usecase.png\n";
```
