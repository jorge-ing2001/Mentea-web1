<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Notificaciones</h2>
            @if(Auth::user()->unreadNotifications->isNotEmpty())
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="text-sm text-teal-600 hover:underline">Marcar todas como leídas</button>
                </form>
            @endif
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-lg text-sm">{{ session('success') }}</div>
            @endif
            <div class="bg-white rounded-xl shadow-sm border divide-y">
                @forelse($notifications as $n)
                    @php $data = $n->data; @endphp
                    <a href="{{ route('notifications.read', $n->id) }}"
                       class="block px-5 py-4 hover:bg-gray-50 transition {{ $n->read_at ? 'opacity-70' : 'bg-teal-50/40' }}">
                        <div class="flex justify-between gap-3">
                            <div>
                                <div class="font-medium text-sm text-gray-900">{{ $data['title'] ?? 'Aviso' }}</div>
                                <div class="text-sm text-gray-600 mt-0.5">{{ $data['body'] ?? '' }}</div>
                                <div class="text-xs text-gray-400 mt-1">{{ $n->created_at->diffForHumans() }}</div>
                            </div>
                            @unless($n->read_at)
                                <span class="shrink-0 w-2 h-2 mt-2 rounded-full bg-teal-500"></span>
                            @endunless
                        </div>
                    </a>
                @empty
                    <div class="px-5 py-10 text-center text-gray-500 text-sm">No tienes notificaciones.</div>
                @endforelse
            </div>
            <div class="mt-4">{{ $notifications->links() }}</div>
        </div>
    </div>
</x-app-layout>
