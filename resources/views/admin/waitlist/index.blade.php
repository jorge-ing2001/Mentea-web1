<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Lista de espera</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-lg text-sm">{{ session('success') }}</div>
            @endif
            <form method="GET" class="mb-4">
                <select name="status" onchange="this.form.submit()" class="border-gray-300 rounded-lg text-sm">
                    <option value="">Todos</option>
                    @foreach(['waiting','notified','assigned','cancelled'] as $st)
                        <option value="{{ $st }}" @selected(request('status')===$st)>{{ $st }}</option>
                    @endforeach
                </select>
            </form>
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <table class="min-w-full divide-y text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left">Paciente</th>
                            <th class="px-4 py-3 text-left">Preferencia</th>
                            <th class="px-4 py-3 text-left">Fecha pref.</th>
                            <th class="px-4 py-3 text-left">Estado</th>
                            <th class="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($entries as $e)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-medium">{{ $e->patient->name ?? '—' }}</div>
                                    <div class="text-xs text-gray-500">{{ $e->patient->email ?? '' }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    {{ $e->specialty ?? '—' }}
                                    @if($e->professional)<br><span class="text-xs text-gray-500">{{ $e->professional->name }}</span>@endif
                                </td>
                                <td class="px-4 py-3">{{ $e->preferred_date?->format('d/m/Y') ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100">{{ $e->status }}</span>
                                    @if($e->assigned_at)
                                        <div class="text-xs text-gray-500 mt-1">Asignada {{ $e->assigned_at->format('d/m/Y H:i') }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    @if(in_array($e->status, ['waiting', 'notified']))
                                        <a href="{{ route('admin.waitlist.assign', $e) }}" class="text-teal-600 hover:underline font-medium">Asignar cita</a>
                                    @endif
                                    @if($e->assigned_appointment_id)
                                        <a href="{{ route('admin.appointments.show', $e->assigned_appointment_id) }}" class="text-gray-600 hover:underline">Ver cita</a>
                                    @endif
                                    <form method="POST" action="{{ route('admin.waitlist.status', $e) }}" class="inline-flex gap-1 items-center">
                                        @csrf @method('PATCH')
                                        <select name="status" class="border-gray-300 rounded text-xs">
                                            @foreach(['waiting','notified','assigned','cancelled'] as $st)
                                                <option value="{{ $st }}" @selected($e->status===$st)>{{ $st }}</option>
                                            @endforeach
                                        </select>
                                        <button class="text-teal-600 text-xs hover:underline">OK</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Sin solicitudes.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $entries->links() }}</div>
        </div>
    </div>
</x-app-layout>
