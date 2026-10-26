<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nuevo profesional</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('admin.professionals.store') }}" class="bg-white rounded-xl shadow-sm border p-6 space-y-4">
                @csrf
                @include('admin.professionals._form')
                <div class="flex gap-3 pt-2">
                    <a href="{{ route('admin.professionals.index') }}" class="px-4 py-2 border rounded-lg">Cancelar</a>
                    <button type="submit" class="flex-1 bg-teal-600 text-white py-2 rounded-lg hover:bg-teal-700">Crear</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
