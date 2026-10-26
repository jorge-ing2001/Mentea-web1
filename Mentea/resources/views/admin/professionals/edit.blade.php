<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar — {{ $professional->name }}</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if(session('success'))
                <div class="p-3 bg-green-100 text-green-800 rounded-lg text-sm">{{ session('success') }}</div>
            @endif
            <form method="POST" action="{{ route('admin.professionals.update', $professional) }}" class="bg-white rounded-xl shadow-sm border p-6 space-y-4">
                @csrf @method('PUT')
                @include('admin.professionals._form', ['professional' => $professional])
                <div class="flex gap-3 pt-2">
                    <a href="{{ route('admin.professionals.index') }}" class="px-4 py-2 border rounded-lg">Volver</a>
                    <a href="{{ route('admin.schedules.edit', $professional) }}" class="px-4 py-2 border rounded-lg text-teal-600">Horarios</a>
                    <button type="submit" class="flex-1 bg-teal-600 text-white py-2 rounded-lg hover:bg-teal-700">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
