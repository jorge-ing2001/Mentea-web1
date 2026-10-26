<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Solicitud #{{ $waitlist->id }}</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if($errors->any())
                <div class="p-3 bg-red-50 text-red-700 rounded-lg text-sm">{{ $errors->first() }}</div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border p-6 space-y-3 text-sm">
                <div class="flex justify-between items-start">
                    <div class="font-medium text-base">{{ $waitlist->specialty }}</div>
                    <span class="px-2.5 py-1 text-xs rounded-full bg-{{ $waitlist->status->color() }}-100 text-{{ $waitlist->status->color() }}-800">
                        {{ $waitlist->status->emoji() }} {{ $waitlist->status->label() }}
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <div class="text-gray-500">Profesional</div>
                        <div>{{ $waitlist->professional?->name ?? 'Cualquier profesional' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Modalidad</div>
                        <div>{{ $waitlist->modality?->label() ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Rango de fechas</div>
                        <div>
                            @if($waitlist->date_from && $waitlist->date_to)
                                {{ $waitlist->date_from->format('d/m/Y') }} – {{ $waitlist->date_to->format('d/m/Y') }}
                            @else — @endif
                        </div>
                    </div>
                    <div>
                        <div class="text-gray-500">Horario preferido</div>
                        <div>
                            @if($waitlist->time_from && $waitlist->time_to)
                                {{ \Carbon\Carbon::parse($waitlist->time_from)->format('H:i') }} – {{ \Carbon\Carbon::parse($waitlist->time_to)->format('H:i') }}
                            @else Cualquiera @endif
                        </div>
                    </div>
                    <div>
                        <div class="text-gray-500">Creada</div>
                        <div>{{ $waitlist->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    @if($waitlist->cancelled_at)
                        <div><div class="text-gray-500">Cancelada</div><div>{{ $waitlist->cancelled_at->format('d/m/Y H:i') }}</div></div>
                    @endif
                    @if($waitlist->expired_at)
                        <div><div class="text-gray-500">Vencida</div><div>{{ $waitlist->expired_at->format('d/m/Y H:i') }}</div></div>
                    @endif
                    @if($waitlist->confirmed_at)
                        <div><div class="text-gray-500">Confirmada</div><div>{{ $waitlist->confirmed_at->format('d/m/Y H:i') }}</div></div>
                    @endif
                </div>
                @if($waitlist->notes)
                    <div class="text-gray-500">Notas: <span class="text-gray-800">{{ $waitlist->notes }}</span></div>
                @endif
            </div>

            @if($waitlist->isOfferActive())
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 space-y-4">
                    <h3 class="font-medium text-blue-900">Disponibilidad encontrada</h3>
                    <div class="text-sm text-blue-900 space-y-1">
                        <p><strong>Profesional:</strong> {{ $waitlist->offeredProfessional?->name }}</p>
                        <p><strong>Fecha:</strong> {{ $waitlist->offered_date?->format('d/m/Y') }} a las {{ \Carbon\Carbon::parse($waitlist->offered_time)->format('H:i') }}</p>
                        <p><strong>Modalidad:</strong> {{ $waitlist->offered_modality?->label() }}</p>
                    </div>
                    <p class="text-sm text-blue-800">
                        Disponibilidad reservada durante
                        <span class="font-mono font-bold text-lg countdown" data-expires="{{ $waitlist->offer_expires_at->toIso8601String() }}">--:--</span>
                        minutos.
                    </p>
                    <div class="flex flex-wrap gap-3">
                        <form method="POST" action="{{ route('patient.waitlist.confirm', $waitlist) }}">
                            @csrf
                            <button type="submit" class="px-5 py-2.5 bg-teal-600 text-white rounded-lg hover:bg-teal-700 font-medium">Confirmar cita</button>
                        </form>
                        <form method="POST" action="{{ route('patient.waitlist.reject', $waitlist) }}" onsubmit="return confirm('¿Rechazar esta disponibilidad?');">
                            @csrf
                            <button type="submit" class="px-5 py-2.5 border border-gray-300 rounded-lg hover:bg-white">Rechazar</button>
                        </form>
                    </div>
                </div>
            @endif

            @if($waitlist->status->value === 'waiting')
                <form method="POST" action="{{ route('patient.waitlist.cancel', $waitlist) }}"
                      onsubmit="return confirm('¿Estás seguro de que deseas retirar esta solicitud de la lista de espera?');">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 border border-red-200 text-red-700 rounded-lg hover:bg-red-50 text-sm">
                        Salir de lista de espera
                    </button>
                </form>
            @endif

            @if($waitlist->assigned_appointment_id)
                <a href="{{ route('patient.appointments.show', $waitlist->assigned_appointment_id) }}" class="block text-center text-teal-600 hover:underline text-sm">
                    Ver cita generada →
                </a>
            @endif

            <a href="{{ route('patient.waitlist.index') }}" class="block text-center text-gray-500 text-sm hover:underline">Volver al listado</a>
        </div>
    </div>
    <script>
        function tick() {
            document.querySelectorAll('.countdown').forEach(el => {
                const exp = new Date(el.dataset.expires).getTime();
                const diff = Math.max(0, Math.floor((exp - Date.now()) / 1000));
                const m = String(Math.floor(diff / 60)).padStart(2, '0');
                const s = String(diff % 60).padStart(2, '0');
                el.textContent = m + ':' + s;
                if (diff === 0) el.closest('.bg-blue-50')?.classList.add('opacity-50');
            });
        }
        tick(); setInterval(tick, 1000);
    </script>
</x-app-layout>
