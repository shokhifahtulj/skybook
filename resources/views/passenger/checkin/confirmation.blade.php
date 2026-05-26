<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Check-In - Confirmation</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 min-h-screen font-sans flex items-center justify-center p-4">
    <div class="max-w-xl w-full">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden text-center relative">
            <div class="h-32 bg-emerald-600 absolute top-0 left-0 w-full"></div>
            
            <div class="relative pt-20 px-8 pb-10">
                <div class="w-24 h-24 bg-white rounded-full mx-auto flex items-center justify-center shadow-lg border-4 border-emerald-50 mb-6 relative z-10">
                    <div class="w-20 h-20 bg-emerald-500 rounded-full flex items-center justify-center text-white">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                </div>
                
                <h1 class="text-3xl font-black text-slate-800 mb-2">Check-in Successful!</h1>
                <p class="text-slate-500 font-medium mb-8">Passenger Checked In Successfully.</p>

                <div class="bg-slate-50 rounded-xl p-6 mb-8 text-left border border-slate-100">
                    <div class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4 border-b border-slate-200 pb-2">Operational Timeline</div>
                    <ul class="space-y-4">
                        <li class="flex items-center gap-3 text-sm font-semibold text-slate-700">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Booking Confirmed
                        </li>
                        <li class="flex items-center gap-3 text-sm font-semibold text-slate-700">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Ticket Issued
                        </li>
                        <li class="flex items-center gap-3 text-sm font-semibold text-slate-700">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Checked In
                        </li>
                        
                        @php
                            $sp = $booking->segments->first()->segmentPassengers->first(); // Simplifikasi untuk demo
                            $bp = \App\Models\BoardingPass::where('booking_segment_passenger_id', $sp->id)->whereIn('status', ['generated', 'active'])->first();
                        @endphp

                        @if($bp && $bp->pdf_path)
                            <li class="flex flex-col gap-2 mt-6 pt-4 border-t border-slate-200">
                                <span class="text-sm font-bold text-slate-800">Boarding Pass Ready</span>
                                <a href="{{ route('boarding-pass.download', $bp->id) }}" class="inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-xl transition-colors shadow-lg shadow-emerald-600/30">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    Download Boarding Pass
                                </a>
                                <div class="text-xs text-slate-400 mt-2">
                                    Simulate Gate Scanner: <a href="{{ route('boarding-pass.verify', ['uuid' => $bp->id, 'signature' => $bp->qr_signature]) }}" class="text-emerald-600 underline" target="_blank">Scan URL</a>
                                </div>
                            </li>
                        @else
                            <li class="flex items-center gap-3 text-sm font-medium text-slate-400">
                                <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Boarding pass will be available shortly...
                            </li>
                        @endif
                    </ul>
                </div>

                <a href="{{ route('manage-booking.index') }}" class="inline-block w-full bg-slate-800 hover:bg-slate-900 text-white font-bold py-3 px-4 rounded-xl transition-colors">
                    Back to Manage Booking
                </a>
            </div>
        </div>
    </div>
</body>
</html>
