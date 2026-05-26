<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-[28px] font-bold text-slate-900">Web Check-In</h2>
                <p class="mt-1 text-sm text-slate-500">Ready to fly? Find your booking and continue to passenger check-in.</p>
            </div>
            <div class="rounded-[18px] border border-slate-200 bg-white px-4 py-3">
                <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Check-in portal</p>
                <p class="mt-1 text-sm font-semibold text-slate-900">Use the shared dashboard shell to continue your journey.</p>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto max-w-2xl">
        <x-admin.card title="Find your booking" description="Enter your PNR and last name to access the check-in workflow.">
            @if(session('error'))
                <div class="mb-6 rounded-[16px] border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('checkin.lookup') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="pnr" class="mb-2 block text-sm font-semibold text-slate-700">Booking Reference (PNR)</label>
                    <input id="pnr" type="text" name="pnr" required maxlength="6" placeholder="e.g. ABC123" class="w-full rounded-[16px] border border-slate-200 bg-white px-4 py-3 text-lg tracking-[0.2em] text-slate-700 outline-none transition focus:border-emerald-300">
                </div>

                <div>
                    <label for="last_name" class="mb-2 block text-sm font-semibold text-slate-700">Last Name</label>
                    <input id="last_name" type="text" name="last_name" required placeholder="e.g. Doe" class="w-full rounded-[16px] border border-slate-200 bg-white px-4 py-3 text-lg text-slate-700 outline-none transition focus:border-emerald-300">
                </div>

                <div class="pt-2">
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-[16px] bg-emerald-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">
                        Find Booking
                    </button>
                </div>
            </form>
        </x-admin.card>
    </div>
</x-app-layout>
