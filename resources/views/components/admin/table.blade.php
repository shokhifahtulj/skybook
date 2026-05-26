@props(['headers' => [], 'emptyMessage' => 'Tidak ada data ditemukan.'])

<div class="overflow-x-auto rounded-[24px] border border-slate-200 bg-white">
    <table class="min-w-full text-left text-sm text-slate-600">
        <thead class="bg-[#F5F6FA] text-[11px] uppercase tracking-[0.18em] text-slate-700">
            <tr>
                @foreach($headers as $header)
                    <th class="px-6 py-4 font-semibold">{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-[#EAEAEA] bg-white">
            {{ $slot }}

            @if(trim($slot) === '')
                <tr>
                    <td colspan="{{ count($headers) }}" class="px-6 py-12 text-center text-slate-500">
                        {{ $emptyMessage }}
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>