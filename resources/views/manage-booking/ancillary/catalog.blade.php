<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Services - {{ $booking->pnr }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 min-h-screen font-sans pb-20">
    <div class="bg-indigo-600 pb-32 pt-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white tracking-tight">Add Services</h1>
                    <p class="text-indigo-100 mt-1 font-medium">PNR: <span class="tracking-widest">{{ $booking->pnr }}</span></p>
                </div>
                <a href="{{ URL::signedRoute('manage-booking.portal', ['pnr' => $booking->pnr]) }}" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg backdrop-blur-md border border-white/20 transition-colors font-medium">
                    Back to Portal
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 -mt-24 space-y-6">
        
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 p-4 rounded-xl flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <h2 class="text-xl font-bold text-slate-800">Available Services</h2>
                <p class="text-sm text-slate-500 mt-1">Enhance your journey with our add-on services.</p>
            </div>
            
            <div class="p-6 space-y-8">
                @foreach($catalog as $service)
                <div class="border border-slate-200 rounded-xl p-5 flex flex-col md:flex-row gap-6 items-start md:items-center hover:border-indigo-300 hover:shadow-md transition-all">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="bg-indigo-50 text-indigo-700 text-xs font-bold px-2 py-1 rounded uppercase tracking-wider">{{ $service->type }}</span>
                            <h3 class="text-lg font-bold text-slate-800">{{ $service->name }}</h3>
                        </div>
                        <p class="text-sm text-slate-600 leading-relaxed">{{ $service->description }}</p>
                        <div class="mt-3 text-xl font-black text-slate-900">
                            Rp {{ number_format($service->base_price, 0, ',', '.') }}
                        </div>
                    </div>
                    
                    <div class="w-full md:w-auto bg-slate-50 p-4 rounded-xl border border-slate-100">
                        <form action="{{ URL::signedRoute('manage-booking.ancillary.store', ['pnr' => $booking->pnr]) }}" method="POST" class="flex flex-col gap-3">
                            @csrf
                            <input type="hidden" name="service_code" value="{{ $service->code }}">
                            
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Select Passenger</label>
                                <select name="passenger_id" required class="w-full text-sm rounded-lg border-slate-200 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">-- Choose --</option>
                                    @foreach($booking->passengers as $passenger)
                                        <option value="{{ $passenger->id }}">{{ $passenger->first_name }} {{ $passenger->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold py-2.5 px-4 rounded-lg transition-colors mt-1">
                                Add & Pay
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        
    </div>
</body>
</html>
