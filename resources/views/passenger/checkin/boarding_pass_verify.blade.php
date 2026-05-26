<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scanner Verification</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-900 min-h-screen font-sans text-white p-4 flex flex-col">
    <!-- Header -->
    <header class="flex justify-between items-center py-4 border-b border-slate-800 mb-8">
        <div class="font-bold text-xl tracking-wider text-slate-300">GATE SCANNER <span class="text-emerald-500 font-black">PRO</span></div>
        <div class="text-sm text-slate-500 font-mono">{{ now()->format('H:i:s UTC') }}</div>
    </header>

    <main class="flex-1 flex flex-col max-w-3xl mx-auto w-full">
        <!-- Status Banner -->
        <div id="scan-result" class="rounded-2xl p-6 mb-8 text-center hidden transition-all duration-300 border-2">
            <div id="scan-icon" class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center border-4"></div>
            <h2 id="scan-title" class="text-3xl font-black mb-2 tracking-tight uppercase"></h2>
            <p id="scan-message" class="text-lg font-medium opacity-90"></p>
        </div>

        <!-- Boarding Pass Info -->
        <div class="bg-slate-800 rounded-3xl p-8 border border-slate-700 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 right-0 p-6 opacity-10">
                <svg class="w-48 h-48" fill="currentColor" viewBox="0 0 24 24"><path d="M21,16V14L13,9V3.5A1.5,1.5 0 0,0 11.5,2A1.5,1.5 0 0,0 10,3.5V9L2,14V16L10,13.5V19L8,20.5V22L11.5,21L15,22V20.5L13,19V13.5L21,16Z"/></svg>
            </div>

            <div class="relative z-10">
                <div class="flex justify-between items-end border-b border-slate-700 pb-6 mb-6">
                    <div>
                        <div class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-1">Passenger</div>
                        <div class="text-4xl font-black text-white">{{ $boardingPass->segmentPassenger->passenger->last_name }}</div>
                        <div class="text-xl font-medium text-slate-300">{{ $boardingPass->segmentPassenger->passenger->first_name }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-1">Status</div>
                        <div class="text-2xl font-bold uppercase {{ $boardingPass->status === 'boarded' ? 'text-emerald-400' : 'text-amber-400' }}">{{ $boardingPass->status }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div>
                        <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Flight</div>
                        <div class="text-xl font-bold">{{ $boardingPass->segmentPassenger->segment->schedule->flight->flight_number }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Seat</div>
                        <div class="text-2xl font-black text-amber-400">{{ $boardingPass->segmentPassenger->seat->seat_number ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Gate</div>
                        <div class="text-xl font-bold">{{ $boardingPass->gate_snapshot ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Group</div>
                        <div class="text-xl font-bold">{{ $boardingPass->boarding_group_snapshot ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-10 flex justify-center gap-4">
            <button id="btn-scan" class="bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xl py-4 px-12 rounded-2xl shadow-lg shadow-emerald-600/20 transition-all active:scale-95">
                EXECUTE BOARDING SCAN
            </button>
        </div>
    </main>

    <script>
        document.getElementById('btn-scan').addEventListener('click', async () => {
            const btn = document.getElementById('btn-scan');
            btn.disabled = true;
            btn.innerHTML = 'SCANNING...';
            btn.classList.add('opacity-75', 'cursor-not-allowed');

            try {
                const response = await fetch('/api/boarding-pass/validate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        uuid: '{{ $boardingPass->id }}',
                        signature: '{{ $signature }}'
                    })
                });

                const data = await response.json();
                const banner = document.getElementById('scan-result');
                const icon = document.getElementById('scan-icon');
                const title = document.getElementById('scan-title');
                const message = document.getElementById('scan-message');

                banner.classList.remove('hidden');

                if (response.ok && data.success) {
                    banner.className = 'rounded-2xl p-6 mb-8 text-center transition-all duration-300 border-2 bg-emerald-900/30 border-emerald-500 text-emerald-400';
                    icon.className = 'w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center border-4 border-emerald-400 bg-emerald-500/20';
                    icon.innerHTML = '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>';
                    title.innerText = 'BOARDING APPROVED';
                    btn.classList.add('hidden'); // Sembunyikan tombol jika sukses
                } else {
                    banner.className = 'rounded-2xl p-6 mb-8 text-center transition-all duration-300 border-2 bg-red-900/30 border-red-500 text-red-400';
                    icon.className = 'w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center border-4 border-red-400 bg-red-500/20';
                    icon.innerHTML = '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>';
                    title.innerText = 'BOARDING DENIED';
                    btn.disabled = false;
                    btn.innerHTML = 'RETRY SCAN';
                    btn.classList.remove('opacity-75', 'cursor-not-allowed');
                }
                
                message.innerText = data.message;

            } catch (error) {
                console.error(error);
                alert('Connection error!');
                btn.disabled = false;
                btn.innerHTML = 'EXECUTE BOARDING SCAN';
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
            }
        });
    </script>
</body>
</html>
