<x-admin-layout>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-2xl font-bold text-slate-800">Tambah Maskapai Baru</h2>
    </div>

    <x-admin.card>
        <form action="{{ route('admin.airlines.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('admin.airlines._form')
        </form>
    </x-admin.card>
</x-admin-layout>