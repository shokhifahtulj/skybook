<x-admin-layout>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-2xl font-bold text-slate-800">Edit Maskapai: {{ $airline->name }}</h2>
    </div>

    <x-admin.card>
        <form action="{{ route('admin.airlines.update', $airline) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.airlines._form')
        </form>
    </x-admin.card>
</x-admin-layout>