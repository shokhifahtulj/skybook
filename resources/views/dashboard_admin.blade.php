<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Booking Pesawat</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 40px; background-color: #f4f7f6; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .logout-btn { background-color: #e3342f; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold; }
        
        /* Card Form */
        .card { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 30px; }
        .card h3 { margin-top: 0; color: #333; }
        .form-group { display: flex; gap: 10px; flex-wrap: wrap; }
        input { padding: 10px; border: 1px solid #ddd; border-radius: 4px; flex: 1; min-width: 150px; }
        .btn-simpan { background-color: #3490dc; color: white; border: none; padding: 10px 25px; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .btn-simpan:hover { background-color: #2779bd; }

        /* Alert Messages */
        .alert { padding: 15px; border-radius: 4px; margin-bottom: 20px; font-weight: bold; }
        .alert-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }

        /* Table Style */
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        th { background-color: #3490dc; color: white; padding: 15px; text-align: left; }
        td { padding: 12px 15px; border-bottom: 1px solid #eee; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        tr:hover { background-color: #f1f1f1; }
        .btn-hapus { color: #e3342f; background: none; border: none; cursor: pointer; font-weight: bold; text-decoration: underline; padding: 0; }
    </style>
</head>
<body>

    <div class="header">
        <div>
            <h1>Dashboard Admin</h1>
            <p>Selamat datang, <strong>Admin</strong> <span style="background:#38c172; color:white; padding:2px 8px; border-radius:10px; font-size:12px;">Administrator</span></p>
        </div>
        <a href="{{ route('logout') }}" class="logout-btn">Keluar (Logout)</a>
    </div>

    @if(session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <h3>Tambah Jadwal Baru (Deteksi Bentrok Otomatis)</h3>
        <form action="{{ url('/flights/store') }}" method="POST" class="form-group">
            @csrf
            <input type="text" name="nomor_penerbangan" placeholder="No. Pesawat (Contoh: GA123)" required>
            <input type="text" name="maskapai" placeholder="Maskapai (Contoh: Garuda)" required>
            <input type="text" name="tujuan" placeholder="Tujuan (Contoh: Bali)" required>
            <input type="datetime-local" name="waktu_berangkat" required>
            <button type="submit" class="btn-simpan">Simpan Jadwal</button>
        </form>
    </div>

    <h3>Daftar Jadwal Penerbangan (Data Real-time)</h3>
    <table>
        <thead>
            <tr>
                <th>No. Penerbangan</th>
                <th>Maskapai</th>
                <th>Tujuan</th>
                <th>Waktu Berangkat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($flights as $f)
            <tr>
                <td>{{ $f->nomor_penerbangan }}</td>
                <td>{{ $f->maskapai }}</td>
                <td>{{ $f->tujuan }}</td>
                <td>{{ \Carbon\Carbon::parse($f->waktu_berangkat)->format('d M Y, H:i') }} WIB</td>
                <td>
                    <form action="{{ route('flights.destroy', $f->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal {{ $f->nomor_penerbangan }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-hapus">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 20px;">Belum ada jadwal penerbangan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>