<x-admin-layout>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-2xl font-bold text-slate-800">Edit Pesawat</h2>
    </div>

    <x-admin.card>
        <form action="{{ route('admin.aircrafts.update', $aircraft) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.aircrafts._form')
        </form>
    </x-admin.card>
</x-admin-layout>
