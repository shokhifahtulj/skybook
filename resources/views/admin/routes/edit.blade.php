<x-admin-layout>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-2xl font-bold text-slate-800">Edit Rute</h2>
    </div>

    <x-admin.card>
        <form action="{{ route('admin.routes.update', $route) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.routes._form')
        </form>
    </x-admin.card>
</x-admin-layout>
