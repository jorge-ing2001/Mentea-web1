<x-app-layout>
    <x-slot name="header">
        <h2 class="mentea-page-title">Detalle de auditoría #{{ $log->id }}</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="mentea-card p-6 space-y-3 text-sm">
                <div><span class="text-slate-500">Acción:</span> <strong>{{ $log->action }}</strong></div>
                <div><span class="text-slate-500">Usuario:</span> {{ $log->user?->name ?? 'Sistema' }}</div>
                <div><span class="text-slate-500">Fecha:</span> {{ $log->created_at->format('d/m/Y H:i:s') }}</div>
                <div><span class="text-slate-500">IP:</span> {{ $log->ip_address ?? '—' }}</div>
                <div><span class="text-slate-500">Agente:</span> <span class="text-xs break-all">{{ $log->user_agent }}</span></div>
                @if($log->old_values)
                    <div>
                        <div class="text-slate-500 mb-1">Valores anteriores</div>
                        <pre class="bg-slate-50 p-3 rounded-lg text-xs overflow-auto">{{ json_encode($log->old_values, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                @endif
                @if($log->new_values)
                    <div>
                        <div class="text-slate-500 mb-1">Valores nuevos</div>
                        <pre class="bg-slate-50 p-3 rounded-lg text-xs overflow-auto">{{ json_encode($log->new_values, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                @endif
            </div>
            <a href="{{ route('super.audit.index') }}" class="inline-block mt-4 text-sm text-slate-500 hover:underline">← Volver</a>
        </div>
    </div>
</x-app-layout>
