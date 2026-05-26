<x-admin-layout>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Layout Kursi Pesawat: {{ $aircraft->model }}</h2>
            <p class="text-sm text-slate-500">Kapasitas Maksimal: {{ $aircraft->capacity }} kursi | Maskapai: {{ $aircraft->airline->name }}</p>
        </div>
        <div>
            <a href="{{ route('admin.aircrafts.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-200 px-5 py-2.5 text-center text-sm font-medium text-slate-600 transition hover:bg-slate-50">
                Kembali ke Daftar
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Sidebar Controls -->
        <div class="space-y-6">
            <x-admin.card>
                <h3 class="mb-4 text-lg font-bold text-slate-800">Generate Layout Otomatis</h3>
                <p class="mb-4 text-sm text-slate-600">Pilih preset pesawat untuk membuat konfigurasi baris dan kolom secara instan.</p>
                
                <form action="{{ route('admin.aircrafts.seats.generate', $aircraft) }}" method="POST" onsubmit="return confirm('Peringatan: Membuat layout baru akan menghapus semua data kursi yang sudah ada pada pesawat ini. Lanjutkan?');">
                    @csrf
                    <div class="mb-4">
                        <label class="mb-2 block text-sm font-medium text-slate-700">Pilih Preset</label>
                        <select name="preset" class="block w-full rounded-lg border border-slate-300 bg-slate-50 p-2.5 text-sm text-slate-900 focus:border-skybook-primary focus:ring-skybook-primary" required>
                            <option value="">Pilih Preset...</option>
                            @foreach($presets as $key => $preset)
                                <option value="{{ $key }}">{{ $preset['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="w-full rounded-lg bg-skybook-primary px-5 py-2.5 text-center text-sm font-medium text-white transition hover:bg-skybook-primary/90 focus:ring-4 focus:ring-skybook-primary/30">
                        Generate Seat Map
                    </button>
                </form>
            </x-admin.card>

            @if($seats->count() > 0)
            <x-admin.card>
                <h3 class="mb-4 text-lg font-bold text-red-600">Zona Berbahaya</h3>
                <form action="{{ route('admin.aircrafts.seats.destroyAll', $aircraft) }}" method="POST" onsubmit="return confirm('APAKAH ANDA YAKIN? Semua layout kursi pesawat ini akan dihapus secara permanen.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full rounded-lg bg-red-600 px-5 py-2.5 text-center text-sm font-medium text-white transition hover:bg-red-700 focus:ring-4 focus:ring-red-300">
                        Hapus Semua Kursi
                    </button>
                </form>
            </x-admin.card>
            @endif

            <x-admin.card>
                <h3 class="mb-4 text-lg font-bold text-slate-800">Statistik Layout</h3>
                <ul class="space-y-3 text-sm text-slate-600">
                    <li class="flex justify-between border-b pb-2">
                        <span>Total Kursi Digenerate</span>
                        <span class="font-bold text-slate-800">{{ $seats->count() }}</span>
                    </li>
                    <li class="flex justify-between border-b pb-2">
                        <span>Kelas Bisnis</span>
                        <span class="font-bold text-slate-800">{{ $seats->where('cabin_class', 'business')->count() }}</span>
                    </li>
                    <li class="flex justify-between border-b pb-2">
                        <span>Kelas Ekonomi</span>
                        <span class="font-bold text-slate-800">{{ $seats->where('cabin_class', 'economy')->count() }}</span>
                    </li>
                    <li class="flex justify-between pb-2">
                        <span>Baris Darurat (Exit Row)</span>
                        <span class="font-bold text-slate-800">{{ $seats->where('is_exit_row', true)->count() }}</span>
                    </li>
                </ul>
                
                @if($seats->count() > $aircraft->capacity)
                    <div class="mt-4 rounded border-l-4 border-yellow-400 bg-yellow-50 p-4">
                        <div class="flex">
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    <strong>Peringatan:</strong> Total kursi yang digenerate ({{ $seats->count() }}) melebihi kapasitas pesawat ({{ $aircraft->capacity }}).
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </x-admin.card>
        </div>

        <!-- Seat Map View -->
        <div class="lg:col-span-2">
            <x-admin.card class="h-full">
                <h3 class="mb-6 text-lg font-bold text-slate-800 border-b pb-2">Visualisasi Layout</h3>
                
                @if($seats->isEmpty())
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <svg class="mb-4 h-16 w-16 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <h4 class="text-lg font-medium text-slate-700">Belum Ada Layout</h4>
                        <p class="mt-1 text-sm text-slate-500">Silakan pilih preset dari panel di sebelah kiri untuk men-generate layout kursi pesawat ini.</p>
                    </div>
                @else
                    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-slate-50 p-6">
                        <div class="mx-auto flex flex-col gap-4 max-w-fit bg-white p-8 rounded-full shadow-inner border border-slate-300 relative" style="border-radius: 40px 40px 10px 10px;">
                            
                            <!-- Cockpit placeholder -->
                            <div class="w-full h-20 mb-8 border-b-2 border-slate-200 flex items-center justify-center relative">
                                <div class="absolute -top-4 w-1/2 h-16 bg-slate-100 rounded-t-full border border-slate-300"></div>
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest relative z-10">Cockpit</span>
                            </div>

                            @php
                                $groupedSeats = $seats->groupBy('row_number');
                                $letters = $seats->pluck('seat_letter')->unique()->sort()->values()->all();
                                $aisleAfter = null;
                                
                                foreach ($letters as $index => $letter) {
                                    if ($index < count($letters) - 1) {
                                        $seatLeft = $seats->where('seat_letter', $letter)->where('is_aisle', true)->first();
                                        $seatRight = $seats->where('seat_letter', $letters[$index+1])->where('is_aisle', true)->first();
                                        if ($seatLeft && $seatRight) {
                                            $aisleAfter = $letter;
                                            break;
                                        }
                                    }
                                }
                            @endphp

                            <!-- Column Headers -->
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-8"></div> <!-- row number placeholder -->
                                @foreach($letters as $letter)
                                    <div class="w-10 text-center font-bold text-slate-500 text-sm">{{ $letter }}</div>
                                    @if($letter === $aisleAfter)
                                        <div class="w-12 text-center text-xs text-slate-300">Aisle</div> <!-- Aisle gap -->
                                    @endif
                                @endforeach
                            </div>

                            <!-- Rows -->
                            @foreach($groupedSeats as $rowNumber => $rowSeats)
                                @php
                                    $isExitRow = $rowSeats->first()->is_exit_row;
                                    $rowClass = $rowSeats->first()->cabin_class;
                                    $bgClass = 'bg-sky-100 border-sky-300 text-sky-800'; // Economy
                                    if ($rowClass == 'business') $bgClass = 'bg-purple-100 border-purple-300 text-purple-800';
                                    if ($rowClass == 'first') $bgClass = 'bg-yellow-100 border-yellow-300 text-yellow-800';
                                    if ($rowClass == 'premium_economy') $bgClass = 'bg-indigo-100 border-indigo-300 text-indigo-800';
                                @endphp

                                @if($isExitRow)
                                    <div class="w-full flex items-center justify-between my-2 relative">
                                        <div class="h-px bg-red-300 w-full absolute top-1/2 -z-10"></div>
                                        <span class="text-xs font-bold text-red-500 bg-white px-2 border border-red-300 rounded-full mx-auto">EXIT ROW</span>
                                    </div>
                                @endif

                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-8 text-center font-bold text-slate-400 text-sm">{{ $rowNumber }}</div>
                                    
                                    @foreach($letters as $letter)
                                        @php
                                            $seat = $rowSeats->where('seat_letter', $letter)->first();
                                        @endphp

                                        @if($seat)
                                            <div class="w-10 h-10 rounded-t-lg rounded-b-sm border-2 {{ $bgClass }} flex items-center justify-center text-xs font-bold shadow-sm cursor-help transition-transform hover:scale-110" title="Seat {{ $seat->seat_number }} ({{ ucfirst($seat->cabin_class) }})">
                                                {{ $letter }}
                                            </div>
                                        @else
                                            <div class="w-10 h-10"></div>
                                        @endif

                                        @if($letter === $aisleAfter)
                                            <div class="w-12 border-x border-dashed border-slate-200"></div> <!-- Aisle gap -->
                                        @endif
                                    @endforeach
                                </div>
                            @endforeach

                            <!-- Rear section placeholder -->
                            <div class="w-full h-12 mt-8 border-t-2 border-slate-200 flex items-center justify-center">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Lavatory / Galley</span>
                            </div>

                        </div>
                    </div>
                    
                    <div class="mt-6 flex flex-wrap gap-4 text-sm text-slate-600">
                        <div class="flex items-center gap-2"><div class="w-4 h-4 rounded bg-sky-100 border border-sky-300"></div> Economy</div>
                        <div class="flex items-center gap-2"><div class="w-4 h-4 rounded bg-indigo-100 border border-indigo-300"></div> Premium Economy</div>
                        <div class="flex items-center gap-2"><div class="w-4 h-4 rounded bg-purple-100 border border-purple-300"></div> Business</div>
                        <div class="flex items-center gap-2"><div class="w-4 h-4 rounded bg-yellow-100 border border-yellow-300"></div> First Class</div>
                    </div>
                @endif
            </x-admin.card>
        </div>
    </div>
</x-admin-layout>
