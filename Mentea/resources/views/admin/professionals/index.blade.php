<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Profesionales</h2>
            <a href="{{ route('admin.professionals.create') }}" class="bg-teal-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-teal-700">+ Nuevo</a>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-lg text-sm">{{ session('success') }}</div>
            @endif
            <form method="GET" class="mb-4 flex flex-wrap gap-2">
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar..." class="border-gray-300 rounded-lg text-sm">
                <select name="specialty" class="border-gray-300 rounded-lg text-sm">
                    <option value="">Todas las especialidades</option>
                    @foreach($specialties as $s)
                        <option value="{{ $s }}" @selected(request('specialty')===$s)>{{ $s }}</option>
                    @endforeach
                </select>
                <select name="status" class="border-gray-300 rounded-lg text-sm">
                    <option value="">Todos los estados</option>
                    <option value="active" @selected(request('status')==='active')>Activos</option>
                    <option value="inactive" @selected(request('status')==='inactive')>Inactivos</option>
                </select>
                <button class="px-3 py-1.5 bg-gray-800 text-white text-sm rounded-lg">Filtrar</button>
            </form>
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <table class="min-w-full divide-y text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left">Nombre</th>
                            <th class="px-4 py-3 text-left">Especialidad</th>
                            <th class="px-4 py-3 text-left">Estado</th>
                            <th class="px-4 py-3 text-left">Citas</th>
                            <th class="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($professionals as $pro)
                            <tr class="{{ $pro->is_available ? '' : 'bg-gray-50 opacity-80' }}">
                                <td class="px-4 py-3">
                                    <div class="font-medium">{{ $pro->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $pro->email }}</div>
                                </td>
                                <td class="px-4 py-3">{{ $pro->specialty }}</td>
                                <td class="px-4 py-3">
                                    @if($pro->is_available)
                                        <span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-800 font-medium">Activo</span>
                                    @else
                                        <span class="px-2 py-0.5 text-xs rounded-full bg-gray-200 text-gray-700 font-medium">Inactivo</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">{{ $pro->appointments_count }}</td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    <a href="{{ route('admin.professionals.edit', $pro) }}" class="text-teal-600 hover:underline">Editar</a>
                                    <a href="{{ route('admin.schedules.edit', $pro) }}" class="text-teal-600 hover:underline">Horarios</a>
                                    <form method="POST" action="{{ route('admin.professionals.toggle', $pro) }}" class="inline"
                                          onsubmit="return confirm('¿{{ $pro->is_available ? 'Inactivar' : 'Activar' }} a {{ $pro->name }}?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-sm {{ $pro->is_available ? 'text-amber-600' : 'text-green-600' }} hover:underline">
                                            {{ $pro->is_available ? 'Inactivar' : 'Activar' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Sin profesionales.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $professionals->links() }}</div>
        </div>
    </div>
</x-app-layout>
