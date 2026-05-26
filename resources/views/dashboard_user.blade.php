<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard User - Cari Tiket</title>
    <style>
        body { font-family: sans-serif; margin: 40px; background-color: #f4f7f6; }
        .header { display: flex; justify-content: space-between; align-items: center; }
        .logout-btn { color: #e3342f; text-decoration: none; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; background: white; margin-top: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #2c3e50; color: white; }
        .btn-pesan { background-color: #38c172; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .alert-success { background-color: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #c3e6cb; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Dashboard User</h1>
        <a href="{{ route('logout') }}" class="logout-btn">Logout</a>
    </div>

    <p>Halo, <strong>{{ Auth::user()->name }}</strong>! Silakan cari dan pesan jadwal penerbangan Anda.</p>
[ <a href="{{ route('bookings.index') }}" style="color: #3490dc; font-weight: bold;">Lihat Riwayat Pesanan Saya</a> ]
</p>
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <h3>Jadwal Penerbangan Tersedia</h3>
    <table>
        <thead>
            <tr>
                <th>Maskapai</th>
                <th>Tujuan</th>
                <th>Waktu Berangkat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($flights as $f)
            <tr>
                <td>{{ $f->maskapai }}</td>
                <td>{{ $f->tujuan }}</td>
                <td>{{ \Carbon\Carbon::parse($f->waktu_berangkat)->format('d M Y, H:i') }} WIB</td>
                <td>
                    <form action="{{ route('bookings.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="flight_id" value="{{ $f->id }}">
                        <button type="submit" class="btn-pesan">Pesan Tiket</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center;">Tidak ada jadwal penerbangan saat ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>