<x-admin-layout>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-2xl font-bold text-slate-800">Edit Bandara: {{ $airport->name }}</h2>
    </div>

    <x-admin.card>
        <form action="{{ route('admin.airports.update', $airport) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.airports._form')
        </form>
    </x-admin.card>
</x-admin-layout>