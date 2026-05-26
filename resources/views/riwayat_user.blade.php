<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Pesanan Saya</title>
    <style>
        body { font-family: sans-serif; margin: 40px; background-color: #f4f7f6; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #3490dc; color: white; }
        .back-link { display: inline-block; margin-bottom: 20px; text-decoration: none; color: #3490dc; font-weight: bold; }
    </style>
</head>
<body>
    <a href="{{ route('dashboard.user') }}" class="back-link">← Kembali ke Dashboard</a>
    
    <div class="card">
        <h2>Riwayat Pemesanan Tiket Anda</h2>
        <table>
            <thead>
                <tr>
                    <th>No. Pesawat</th>
                    <th>Maskapai</th>
                    <th>Tujuan</th>
                    <th>Waktu Berangkat</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($myBookings as $b)
                <tr>
                    <td>{{ $b->flight->nomor_penerbangan }}</td>
                    <td>{{ $b->flight->maskapai }}</td>
                    <td>{{ $b->flight->tujuan }}</td>
                    <td>{{ \Carbon\Carbon::parse($b->flight->waktu_berangkat)->format('d M Y, H:i') }} WIB</td>
                    <td><span style="color: green; font-weight: bold;">Terdaftar</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center;">Anda belum memesan tiket apapun.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>