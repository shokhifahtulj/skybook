<x-admin-layout>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-2xl font-bold text-slate-800">Edit Penerbangan</h2>
    </div>

    <x-admin.card>
        <form action="{{ route('admin.flights.update', $flight) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.flights._form')
        </form>
    </x-admin.card>
</x-admin-layout>
