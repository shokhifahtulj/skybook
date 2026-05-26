<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Ticket</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 min-h-screen font-sans flex items-center justify-center p-4">
    <div class="bg-white max-w-md w-full rounded-3xl shadow-2xl shadow-slate-200/50 p-8 border border-slate-100 text-center">
        @if(isset($error))
            <div class="w-16 h-16 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </div>
            <h1 class="text-2xl font-bold text-slate-800 mb-2">Invalid Ticket</h1>
            <p class="text-slate-500 mb-8">{{ $error }}</p>
        @else
            <div class="w-20 h-20 bg-emerald-100 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-6 ring-4 ring-emerald-50">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight mb-2">Valid Ticket</h1>
            <p class="text-slate-500 font-medium mb-8">This ticket is authentic and confirmed.</p>
            
            <div class="bg-slate-50 rounded-2xl p-6 text-left border border-slate-100 space-y-4">
                <div>
                    <div class="text-xs text-slate-400 font-bold uppercase tracking-wider">Ticket Number</div>
                    <div class="text-lg font-bold text-slate-800">{{ $ticket->ticket_number }}</div>
                </div>
                <div>
                    <div class="text-xs text-slate-400 font-bold uppercase tracking-wider">Passenger</div>
                    <div class="text-lg font-bold text-slate-800">{{ $ticket->snapshot_data['passenger_name'] ?? 'N/A' }}</div>
                </div>
                <div class="flex justify-between items-center border-t border-slate-200 pt-4 mt-2">
                    <div>
                        <div class="text-xs text-slate-400 font-bold uppercase tracking-wider">Flight</div>
                        <div class="font-bold text-slate-800">{{ $ticket->snapshot_data['flight_number'] ?? 'N/A' }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-slate-400 font-bold uppercase tracking-wider">Status</div>
                        <div class="font-bold text-emerald-600 capitalize">{{ $ticket->ticket_status }}</div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</body>
</html>
