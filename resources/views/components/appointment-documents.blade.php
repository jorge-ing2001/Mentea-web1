@props(['appointment'])
@if($appointment->order_document || $appointment->authorization_document)
<div class="border rounded-xl p-4 space-y-3 bg-slate-50">
    <h4 class="font-medium text-sm text-gray-800">Documentos adjuntos</h4>
    <div class="grid sm:grid-cols-2 gap-3">
        <div class="bg-white border rounded-lg p-3">
            <div class="text-xs font-semibold text-teal-700 uppercase tracking-wide mb-2">Orden</div>
            @if($appointment->order_document)
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('documents.view', ['appointment' => $appointment, 'type' => 'order']) }}"
                       target="_blank" rel="noopener"
                       class="inline-flex items-center px-3 py-1.5 text-sm rounded-lg bg-teal-600 text-white hover:bg-teal-700">
                        Ver documento
                    </a>
                    <a href="{{ route('documents.download', ['appointment' => $appointment, 'type' => 'order']) }}"
                       class="inline-flex items-center px-3 py-1.5 text-sm rounded-lg border border-gray-300 hover:bg-gray-50">
                        Descargar
                    </a>
                </div>
            @else
                <p class="text-sm text-gray-400">No adjuntada</p>
            @endif
        </div>
        <div class="bg-white border rounded-lg p-3">
            <div class="text-xs font-semibold text-emerald-700 uppercase tracking-wide mb-2">Autorización</div>
            @if($appointment->authorization_document)
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('documents.view', ['appointment' => $appointment, 'type' => 'authorization']) }}"
                       target="_blank" rel="noopener"
                       class="inline-flex items-center px-3 py-1.5 text-sm rounded-lg bg-emerald-600 text-white hover:bg-emerald-700">
                        Ver documento
                    </a>
                    <a href="{{ route('documents.download', ['appointment' => $appointment, 'type' => 'authorization']) }}"
                       class="inline-flex items-center px-3 py-1.5 text-sm rounded-lg border border-gray-300 hover:bg-gray-50">
                        Descargar
                    </a>
                </div>
            @else
                <p class="text-sm text-gray-400">No adjuntada</p>
            @endif
        </div>
    </div>
</div>
@endif
