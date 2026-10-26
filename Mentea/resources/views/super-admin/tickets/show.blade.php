<x-app-layout>
    <x-slot name="header">
        <h2 class="mentea-page-title">Ticket #{{ $ticket->id }}</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if(session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif
            <div class="mentea-card p-6 space-y-3 text-sm">
                <div><span class="text-slate-500">Tipo:</span> <strong>{{ $ticket->typeLabel() }}</strong>
                    @if($ticket->category)
                        · {{ \App\Models\SupportTicket::categories()[$ticket->category] ?? $ticket->category }}
                    @endif
                </div>
                <div><span class="text-slate-500">Usuario:</span> <strong>{{ $ticket->user?->name }}</strong> ({{ $ticket->user?->email }})</div>
                <div><span class="text-slate-500">Asunto:</span> {{ $ticket->subject }}</div>
                <div><span class="text-slate-500">Mensaje:</span>
                    <p class="mt-1 whitespace-pre-wrap text-slate-800">{{ $ticket->message }}</p>
                </div>
                <div class="text-slate-400 text-xs">Creado {{ $ticket->created_at->format('d/m/Y H:i') }}</div>
            </div>
            <form method="POST" action="{{ route('super.tickets.update', $ticket) }}" class="mentea-card p-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="mentea-label">Estado</label>
                    <select name="status" class="mentea-input">
                        @foreach(['open'=>'Abierto','in_progress'=>'En progreso','resolved'=>'Resuelto','closed'=>'Cerrado'] as $v=>$l)
                            <option value="{{ $v }}" @selected($ticket->status===$v)>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mentea-label">Prioridad</label>
                    <select name="priority" class="mentea-input">
                        @foreach(['low'=>'Baja','medium'=>'Media','high'=>'Alta','urgent'=>'Urgente'] as $v=>$l)
                            <option value="{{ $v }}" @selected($ticket->priority===$v)>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mentea-label">Notas internas</label>
                    <textarea name="admin_notes" rows="3" class="mentea-input">{{ old('admin_notes', $ticket->admin_notes) }}</textarea>
                </div>
                <button type="submit" class="btn-mentea">Actualizar ticket</button>
            </form>
            <a href="{{ route('super.tickets.index') }}" class="text-sm text-slate-500 hover:underline">← Volver al listado</a>
        </div>
    </div>
</x-app-layout>
