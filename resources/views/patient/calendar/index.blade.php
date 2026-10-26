<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-2">
            <h2 class="mentea-page-title">Mi calendario</h2>
            <a href="{{ route('patient.appointments.index') }}" class="text-sm text-teal-700 hover:underline">Vista lista</a>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mentea-card p-4 mb-4 flex flex-wrap gap-4 text-xs" role="list" aria-label="Leyenda de estados">
                <span class="flex items-center gap-1" role="listitem"><span class="w-3 h-3 rounded bg-amber-500" aria-hidden="true"></span> Pendiente</span>
                <span class="flex items-center gap-1" role="listitem"><span class="w-3 h-3 rounded bg-teal-600" aria-hidden="true"></span> Confirmada</span>
                <span class="flex items-center gap-1" role="listitem"><span class="w-3 h-3 rounded bg-slate-500" aria-hidden="true"></span> Completada</span>
                <span class="flex items-center gap-1" role="listitem"><span class="w-3 h-3 rounded bg-red-500" aria-hidden="true"></span> Cancelada</span>
            </div>
            <div class="mentea-card p-4">
                <div id="calendar" role="region" aria-label="Calendario de mis citas"></div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const el = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(el, {
                initialView: 'dayGridMonth',
                locale: 'es',
                headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,listWeek' },
                slotMinTime: '07:00:00',
                slotMaxTime: '21:00:00',
                allDaySlot: false,
                height: 'auto',
                events: @json(route('patient.calendar.events')),
                eventClick: function (info) {
                    if (info.event.url) { info.jsEvent.preventDefault(); window.location.href = info.event.url; }
                }
            });
            calendar.render();
        });
    </script>
</x-app-layout>
