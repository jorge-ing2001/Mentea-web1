<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="mentea-page-title">Soporte</h2>
            <a href="{{ route('patient.tickets.create') }}" class="btn-mentea text-sm">Nueva solicitud</a>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="alert-success mb-4">{{ session('success') }}</div>
            @endif
            <div class="mentea-card divide-y">
                @forelse($tickets as $t)
                    <div class="p-4 text-sm">
                        <div class="font-medium">{{ $t->subject }}</div>
                        <div class="text-slate-500 mt-1">{{ $t->statusLabel() }} · {{ $t->created_at->format('d/m/Y') }}</div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-500 text-sm">No has enviado solicitudes de soporte.</div>
                @endforelse
            </div>
            <div class="mt-4">{{ $tickets->links() }}</div>
        </div>
    </div>
</x-app-layout>
