<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-[28px] font-bold text-slate-900">Riwayat Booking</h2>
                <p class="mt-1 text-sm text-slate-500">Semua pemesanan tiket Anda ditampilkan di sini.</p>
            </div>
            <a href="{{ route('flights.index') }}" class="inline-flex items-center justify-center rounded-[16px] border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Cari Jadwal</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-admin.card title="Riwayat Booking" description="Pantau status, detail, dan tindakan untuk setiap pemesanan aktif Anda.">
            @if(session('success'))
                <div class="mb-4 rounded-[16px] border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">{{ session('success') }}</div>
            @endif

            <div x-data="{ loading: false }" class="space-y-4">
                <form x-on:submit="loading = true" method="GET" action="{{ route('bookings.index') }}" class="rounded-[24px] border border-slate-200 bg-slate-50/70 p-4 sm:p-5">
                    <div class="grid gap-4 lg:grid-cols-[2fr_1fr_auto] lg:items-end">
                        <div>
                            <label for="search" class="mb-2 block text-sm font-semibold text-slate-700">Cari PNR / Flight</label>
                            <input id="search" name="search" value="{{ request('search') }}" placeholder="Contoh: SKY-1234 atau GA-100" class="w-full rounded-[16px] border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 outline-none transition focus:border-sky-300" />
                        </div>

                        <div>
                            <label for="status" class="mb-2 block text-sm font-semibold text-slate-700">Status</label>
                            <select id="status" name="status" class="w-full rounded-[16px] border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 outline-none transition focus:border-sky-300">
                                <option value="">Semua</option>
                                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                <option value="ticketed" {{ request('status') === 'ticketed' ? 'selected' : '' }}>Ticketed</option>
                                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            </select>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <button type="submit" class="inline-flex items-center justify-center rounded-[16px] bg-skybook-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-700">
                                <span x-show="!loading">Filter</span>
                                <span x-show="loading">Menyaring...</span>
                            </button>
                            @if(request()->hasAny(['search', 'status']))
                                <a href="{{ route('bookings.index') }}" class="inline-flex items-center justify-center rounded-[16px] border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Reset</a>
                            @endif
                        </div>
                    </div>
                </form>

                @forelse($bookings as $booking)
                    @php
                        $primarySegment = $booking->segments->first();
                        $flight = $primarySegment?->schedule?->flight ?? $booking->schedule?->flight;
                        $route = $flight?->route;
                        $legacySchedule = $booking->schedule;
                        $departureDateTime = $primarySegment?->schedule?->departure_datetime;

                        if (is_string($departureDateTime) || is_numeric($departureDateTime)) {
                            $departureDateTime = \Carbon\Carbon::parse((string) $departureDateTime);
                        }

                        if (! $departureDateTime instanceof \DateTimeInterface) {
                            $legacyDate = $legacySchedule?->tanggal;
                            $legacyTime = $legacySchedule?->jam_berangkat;

                            if ($legacyDate instanceof \DateTimeInterface) {
                                $parsedDate = $legacyDate->copy();
                            } elseif ($legacyDate) {
                                $parsedDate = \Carbon\Carbon::parse((string) $legacyDate);
                            }

                            if (isset($parsedDate) && $parsedDate instanceof \DateTimeInterface) {
                                if ($legacyTime) {
                                    $parsedTime = $legacyTime instanceof \DateTimeInterface
                                        ? $legacyTime->copy()
                                        : \Carbon\Carbon::parse((string) $legacyTime);

                                    $departureDateTime = $parsedDate->copy()->setTime(
                                        $parsedTime->hour,
                                        $parsedTime->minute,
                                        $parsedTime->second
                                    );
                                } else {
                                    $departureDateTime = $parsedDate;
                                }
                            } elseif ($legacyTime) {
                                $departureDateTime = \Carbon\Carbon::parse((string) $legacyTime);
                            }
                        }

                        $departureDateLabel = $departureDateTime instanceof \DateTimeInterface
                            ? $departureDateTime->format('d M Y')
                            : 'Tanggal belum tersedia';
                        $departureTimeLabel = $departureDateTime instanceof \DateTimeInterface
                            ? $departureDateTime->format('H:i')
                            : 'Tidak tersedia';

                        $status = $booking->booking_status ?? $booking->status_booking ?? 'draft';
                        $paymentStatus = $booking->payment_status ?? 'unpaid';
                        $ticketCount = (int) ($booking->jumlah_tiket ?? $booking->passengers->count() ?? 0);
                        $fareSnapshot = $primarySegment?->fare_snapshot;
                        $computedTotal = null;

                        if ($ticketCount > 0 && is_numeric($fareSnapshot) && (float) $fareSnapshot > 0) {
                            $computedTotal = (float) $fareSnapshot * $ticketCount;
                        }

                        $total = $computedTotal ?? ($booking->total_amount ?: $booking->total_harga);

                        $badgeClasses = match(strtolower((string) $status)) {
                            'ticketed' => 'bg-emerald-100 text-emerald-700',
                            'confirmed' => 'bg-sky-100 text-sky-700',
                            'draft' => 'bg-amber-100 text-amber-700',
                            default => 'bg-slate-100 text-slate-700',
                        };
                    @endphp

                    <div class="rounded-[24px] border border-slate-200 bg-slate-50/70 p-5">
                        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                            <div class="space-y-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">PNR {{ $booking->pnr ?? '—' }}</span>
                                    <span class="rounded-full px-3 py-1 text-xs font-bold {{ $badgeClasses }}">{{ ucfirst($status) }}</span>
                                </div>

                                <h3 class="text-lg font-bold text-slate-900">{{ $flight->flight_number ?? 'Flight' }} — {{ $flight->airline->name ?? 'SkyBook' }}</h3>
                                <p class="text-sm text-slate-600">
                                    {{ $route?->origin?->iata_code ?? 'N/A' }} → {{ $route?->destination?->iata_code ?? 'N/A' }}
                                </p>
                                <p class="text-sm text-slate-500">
                                    {{ $departureDateLabel }} · {{ $departureTimeLabel }}
                                </p>
                                <p class="text-sm text-slate-600">Jumlah tiket: {{ $ticketCount }} · Total Rp {{ number_format((float) $total, 0, ',', '.') }}</p>
                                <p class="text-sm text-slate-500">Status pembayaran: {{ ucfirst($paymentStatus) }}</p>
                            </div>

                            <div class="flex flex-wrap items-center gap-2 md:justify-end">
                                <a href="{{ route('bookings.ticket', $booking) }}" class="rounded-[16px] bg-skybook-primary px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-700">Print E-ticket</a>
                                <form action="{{ route('bookings.destroy', $booking) }}" method="POST" onsubmit="return confirm('Batalkan booking ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-[16px] bg-rose-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-600">Batalkan</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-[24px] border border-dashed border-slate-200 bg-white px-6 py-10 text-center text-slate-500">
                        @if(request()->hasAny(['search', 'status']))
                            Tidak ada booking yang cocok dengan filter Anda. Coba ubah kata kunci atau status.
                        @else
                            Belum ada booking yang tersimpan. Silakan lihat jadwal penerbangan terlebih dahulu.
                        @endif
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $bookings->links() }}
            </div>
        </x-admin.card>
    </div>
</x-app-layout>
