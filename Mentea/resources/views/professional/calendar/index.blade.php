<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mi calendario</h2>
            <a href="{{ route('professional.appointments.index') }}" class="text-sm text-teal-600 hover:underline">Vista lista</a>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border p-4 mb-4 flex flex-wrap gap-4 text-xs">
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-amber-500"></span> Pendiente</span>
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-teal-600"></span> Confirmada</span>
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-green-600"></span> Completada</span>
            </div>
            <div class="bg-white rounded-xl shadow-sm border p-4">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                initialView: 'timeGridWeek',
                locale: 'es',
                headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay' },
                slotMinTime: '07:00:00',
                slotMaxTime: '21:00:00',
                allDaySlot: false,
                height: 'auto',
                events: @json(route('professional.calendar.events')),
                eventClick: function (info) {
                    if (info.event.url) { info.jsEvent.preventDefault(); window.location.href = info.event.url; }
                }
            });
            calendar.render();
        });
    </script>
</x-app-layout>
