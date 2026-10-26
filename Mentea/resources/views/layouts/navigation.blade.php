<nav x-data="{ open: false }" class="mentea-header">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
                        <img src="{{ asset('images/brand/logo-symbol.svg') }}" alt="Mentea" class="h-9 w-9">
                        <span class="hidden sm:inline mentea-brand-wordmark" style="font-size:1.05rem;">MENTEA</span>
                    </a>
                </div>
                <div class="hidden space-x-6 sm:-my-px sm:ms-10 sm:flex items-center">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('*.dashboard')">
                        <x-nav-icon name="home" :active="request()->routeIs('dashboard') || request()->routeIs('*.dashboard')" />
                        Inicio
                    </x-nav-link>

                    
                    @if(Auth::user()->isSuperAdmin())
                        <x-nav-link :href="route('super.dashboard')" :active="request()->routeIs('super.dashboard')">
                            <x-nav-icon name="home" :active="request()->routeIs('super.dashboard')" />
                            Panel
                        </x-nav-link>
                        <x-nav-link :href="route('super.settings.index')" :active="request()->routeIs('super.settings.*')">
                            <x-nav-icon name="settings" :active="request()->routeIs('super.settings.*')" />
                            Configuración
                        </x-nav-link>
                        <x-nav-link :href="route('super.tickets.index')" :active="request()->routeIs('super.tickets.*')">
                            <x-nav-icon name="chat" :active="request()->routeIs('super.tickets.*')" />
                            Tickets
                        </x-nav-link>
                        <x-nav-link :href="route('super.audit.index')" :active="request()->routeIs('super.audit.*')">
                            <x-nav-icon name="list" :active="request()->routeIs('super.audit.*')" />
                            Auditoría
                        </x-nav-link>
                        <x-nav-link :href="route('super.roles.index')" :active="request()->routeIs('super.roles.*')">
                            <x-nav-icon name="users" :active="request()->routeIs('super.roles.*')" />
                            Roles
                        </x-nav-link>
                        <x-nav-link :href="route('super.users.index')" :active="request()->routeIs('super.users.*')">
                            <x-nav-icon name="user" :active="request()->routeIs('super.users.*')" />
                            Usuarios
                        </x-nav-link>
                    @endif

                    @if(Auth::user()->isPatient())
                        <x-nav-link :href="route('patient.appointments.create')" :active="request()->routeIs('patient.appointments.create') || request()->routeIs('patient.appointments.schedule')">
                            <x-nav-icon name="plus" :active="request()->routeIs('patient.appointments.create') || request()->routeIs('patient.appointments.schedule')" />
                            Agendar
                        </x-nav-link>
                        <x-nav-link :href="route('patient.appointments.index')" :active="request()->routeIs('patient.appointments.index') || request()->routeIs('patient.appointments.show')">
                            <x-nav-icon name="calendar" :active="request()->routeIs('patient.appointments.index') || request()->routeIs('patient.appointments.show')" />
                            Mis citas
                        </x-nav-link>
                        <x-nav-link :href="route('patient.waitlist.index')" :active="request()->routeIs('patient.waitlist.*')">
                            <x-nav-icon name="clock" :active="request()->routeIs('patient.waitlist.*')" />
                            Lista de espera
                        </x-nav-link>
                        <x-nav-link :href="route('patient.tickets.index')" :active="request()->routeIs('patient.tickets.*')">
                            <x-nav-icon name="chat" :active="request()->routeIs('patient.tickets.*')" />
                            Soporte
                        </x-nav-link>
                    @endif

                    @if(Auth::user()->isProfessional())
                        <x-nav-link :href="route('professional.appointments.index')" :active="request()->routeIs('professional.appointments.*')">
                            <x-nav-icon name="clipboard" :active="request()->routeIs('professional.appointments.*')" />
                            Mis citas
                        </x-nav-link>
                        <x-nav-link :href="route('professional.calendar.index')" :active="request()->routeIs('professional.calendar.*')">
                            <x-nav-icon name="calendar" :active="request()->routeIs('professional.calendar.*')" />
                            Calendario
                        </x-nav-link>
                    @endif

                    @if(Auth::user()->isAdmin())
                        <x-nav-link :href="route('admin.calendar.index')" :active="request()->routeIs('admin.appointments.*') || request()->routeIs('admin.calendar.*')">
                            <x-nav-icon name="calendar" :active="request()->routeIs('admin.appointments.*') || request()->routeIs('admin.calendar.*')" />
                            Citas
                        </x-nav-link>
                        <x-nav-link :href="route('admin.professionals.index')" :active="request()->routeIs('admin.professionals.*') || request()->routeIs('admin.schedules.*')">
                            <x-nav-icon name="users" :active="request()->routeIs('admin.professionals.*') || request()->routeIs('admin.schedules.*')" />
                            Profesionales
                        </x-nav-link>
                        <x-nav-link :href="route('admin.waitlist.index')" :active="request()->routeIs('admin.waitlist.*')">
                            <x-nav-icon name="list" :active="request()->routeIs('admin.waitlist.*')" />
                            Lista de espera
                        </x-nav-link>
                        <x-nav-link :href="route('admin.events.index')" :active="request()->routeIs('admin.events.*')">
                            <x-nav-icon name="clipboard" :active="request()->routeIs('admin.events.*')" />
                            Diligenciamientos
                        </x-nav-link>
                        <x-nav-link :href="route('admin.exceptions.index')" :active="request()->routeIs('admin.exceptions.*')">
                            <x-nav-icon name="clock" :active="request()->routeIs('admin.exceptions.*')" />
                            Excepciones
                        </x-nav-link>
                        <x-nav-link :href="route('admin.surveys.index')" :active="request()->routeIs('admin.surveys.*')">
                            <x-nav-icon name="chart" :active="request()->routeIs('admin.surveys.*')" />
                            Encuestas
                        </x-nav-link>
                        <x-nav-link :href="route('admin.reports.index')" :active="request()->routeIs('admin.reports.*')">
                            <x-nav-icon name="chart" :active="request()->routeIs('admin.reports.*')" />
                            Reportes
                        </x-nav-link>

                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-1">
    {{-- Campana de notificaciones --}}
    <x-dropdown align="right" width="48">
        <x-slot name="trigger">

            @php
                $unreadCount = Auth::user()->unreadNotifications()->count();
            @endphp

            <button type="button" class="relative inline-flex items-center p-2 rounded-full text-slate-500 hover:text-teal-700 hover:bg-teal-50 focus:outline-none transition">
                
                <x-icon
                    name="bell"
                    class="h-6 w-6 {{ $unreadCount > 0 ? 'mentea-icon--bell-alert' : '' }}"
                    :variant="$unreadCount > 0 ? 'solid' : 'outline'"
                />

                @if($unreadCount > 0)
                    <span class="absolute top-0.5 right-0.5 flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white mentea-notif-dot">
                        {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                    </span>
                @endif

            </button>
        </x-slot>
                    <x-slot name="content">
                        <div class="px-4 py-2 border-b border-gray-100 flex justify-between items-center">
                            <span class="text-sm font-semibold text-gray-800">Notificaciones</span>
                            <a href="{{ route('notifications.index') }}" class="text-xs text-teal-600 hover:underline">Ver todas</a>
                        </div>
                        @forelse(Auth::user()->notifications()->take(6)->get() as $n)
                            @php $d = $n->data; @endphp
                            <a href="{{ route('notifications.read', $n->id) }}" class="block px-4 py-3 text-sm hover:bg-gray-50 border-b border-gray-50 {{ $n->read_at ? '' : 'bg-teal-50/60' }}">
                                <div class="font-medium text-gray-900">{{ $d['title'] ?? 'Aviso' }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ \Illuminate\Support\Str::limit($d['body'] ?? '', 80) }}</div>
                                <div class="text-[10px] text-gray-400 mt-1">{{ $n->created_at->diffForHumans() }}</div>
                            </a>
                        @empty
                            <div class="px-4 py-6 text-center text-sm text-gray-500">Sin notificaciones</div>
                        @endforelse
                        @if(Auth::user()->unreadNotifications()->exists())
                            <form method="POST" action="{{ route('notifications.read-all') }}" class="border-t">
                                @csrf
                                <button type="submit" class="w-full text-center text-xs text-teal-600 py-2.5 hover:bg-gray-50">Marcar todas como leídas</button>
                            </form>
                        @endif
                    </x-slot>
                </x-dropdown>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <div class="px-4 py-2 text-xs text-slate-400">{{ Auth::user()->role->label() }}</div>
                        <x-dropdown-link :href="route('notifications.index')" class="flex items-center gap-2">
                            <x-icon name="bell" class="w-4 h-4 text-teal-600" /> Notificaciones
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('profile.edit')" class="flex items-center gap-2">
                            <x-icon name="user" class="w-4 h-4 text-teal-600" /> Mi cuenta
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" class="flex items-center gap-2" onclick="event.preventDefault(); this.closest('form').submit();">
                                <x-icon name="logout" class="w-4 h-4 text-teal-600" /> Cerrar sesión
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')">
                <x-icon name="home" class="w-4 h-4 text-teal-600" /> Inicio
            </x-responsive-nav-link>
            @if(Auth::user()->isSuperAdmin())
                <x-responsive-nav-link :href="route('super.dashboard')"><x-icon name="home" class="w-4 h-4 text-teal-600" /> Panel</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('super.settings.index')"><x-icon name="settings" class="w-4 h-4 text-teal-600" /> Configuración</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('super.tickets.index')"><x-icon name="chat" class="w-4 h-4 text-teal-600" /> Tickets</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('super.audit.index')"><x-icon name="list" class="w-4 h-4 text-teal-600" /> Auditoría</x-responsive-nav-link>
            @endif

            
                    @if(Auth::user()->isSuperAdmin())
                        <x-nav-link :href="route('super.dashboard')" :active="request()->routeIs('super.dashboard')">
                            <x-nav-icon name="home" :active="request()->routeIs('super.dashboard')" />
                            Panel
                        </x-nav-link>
                        <x-nav-link :href="route('super.settings.index')" :active="request()->routeIs('super.settings.*')">
                            <x-nav-icon name="settings" :active="request()->routeIs('super.settings.*')" />
                            Configuración
                        </x-nav-link>
                        <x-nav-link :href="route('super.tickets.index')" :active="request()->routeIs('super.tickets.*')">
                            <x-nav-icon name="chat" :active="request()->routeIs('super.tickets.*')" />
                            Tickets
                        </x-nav-link>
                        <x-nav-link :href="route('super.audit.index')" :active="request()->routeIs('super.audit.*')">
                            <x-nav-icon name="list" :active="request()->routeIs('super.audit.*')" />
                            Auditoría
                        </x-nav-link>
                        <x-nav-link :href="route('super.roles.index')" :active="request()->routeIs('super.roles.*')">
                            <x-nav-icon name="users" :active="request()->routeIs('super.roles.*')" />
                            Roles
                        </x-nav-link>
                    @endif

                    @if(Auth::user()->isPatient())
                <x-responsive-nav-link :href="route('patient.appointments.create')">
                    <x-icon name="plus" class="w-4 h-4 text-teal-600" /> Agendar cita
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('patient.appointments.index')">
                    <x-icon name="calendar" class="w-4 h-4 text-teal-600" /> Mis citas
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('patient.waitlist.index')">
                    <x-icon name="clock" class="w-4 h-4 text-teal-600" /> Lista de espera
                </x-responsive-nav-link>
            @endif
            @if(Auth::user()->isProfessional())
                <x-responsive-nav-link :href="route('professional.appointments.index')">
                    <x-icon name="clipboard" class="w-4 h-4 text-teal-600" /> Mis citas
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('professional.calendar.index')">
                    <x-icon name="calendar" class="w-4 h-4 text-teal-600" /> Calendario
                </x-responsive-nav-link>
            @endif
            @if(Auth::user()->isAdmin())
                <x-responsive-nav-link :href="route('admin.appointments.index')">
                    <x-icon name="clipboard" class="w-4 h-4 text-teal-600" /> Citas
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.calendar.index')">
                    <x-icon name="calendar" class="w-4 h-4 text-teal-600" /> Calendario
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.professionals.index')">
                    <x-icon name="users" class="w-4 h-4 text-teal-600" /> Profesionales
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.waitlist.index')">
                    <x-icon name="list" class="w-4 h-4 text-teal-600" /> Lista de espera
                </x-responsive-nav-link>
            @endif
            <x-responsive-nav-link :href="route('notifications.index')">
                <x-icon name="bell" class="w-4 h-4 text-teal-600" /> Notificaciones
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('profile.edit')">
                <x-icon name="user" class="w-4 h-4 text-teal-600" /> Mi cuenta
            </x-responsive-nav-link>
        </div>
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('notifications.index')">Notificaciones</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('profile.edit')">Perfil</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                        Cerrar sesión
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
