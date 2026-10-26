{{-- Iconos Mentea — estilo marca (teal, suaves, redondeados) --}}
@props([
    'name' => 'home',
    'class' => 'w-5 h-5',
    'variant' => 'outline', // outline | solid
])

@php
    $isSolid = $variant === 'solid';
    $iconClass = trim(($class ?? 'w-5 h-5') . ' mentea-icon mentea-icon--' . $name);
@endphp

@switch($name)
    @case('home')
        @if($isSolid)
            <svg {{ $attributes->merge(['class' => $iconClass, 'data-mentea-icon' => $name]) }} viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 3.2 3.8 10a1 1 0 0 0-.3.7V20a1.5 1.5 0 0 0 1.5 1.5H9.5v-6h5v6h4.5A1.5 1.5 0 0 0 20.5 20v-9.3a1 1 0 0 0-.3-.7L12 3.2z"/>
            </svg>
        @else
            <svg {{ $attributes->merge(['class' => $iconClass, 'data-mentea-icon' => $name]) }} fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 10.5 12 4l8 6.5V19a1 1 0 0 1-1 1h-4.5v-5.5h-5V20H5a1 1 0 0 1-1-1v-8.5z"/>
            </svg>
        @endif
        @break

    @case('user')
        @if($isSolid)
            <svg {{ $attributes->merge(['class' => $iconClass, 'data-mentea-icon' => $name]) }} viewBox="0 0 24 24" fill="currentColor">
                <circle cx="12" cy="8" r="4"/>
                <path d="M4 20.2c0-3.5 3.6-6.2 8-6.2s8 2.7 8 6.2v.3H4v-.3z"/>
            </svg>
        @else
            <svg {{ $attributes->merge(['class' => $iconClass, 'data-mentea-icon' => $name]) }} fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="8" r="3.5"/>
                <path d="M5 19.5c1.5-3 4-4.5 7-4.5s5.5 1.5 7 4.5"/>
            </svg>
        @endif
        @break

    @case('calendar')
        @if($isSolid)
            <svg {{ $attributes->merge(['class' => $iconClass, 'data-mentea-icon' => $name]) }} viewBox="0 0 24 24" fill="currentColor">
                <path d="M7 3.5a1 1 0 0 1 1 1V6h8V4.5a1 1 0 1 1 2 0V6h1.5A1.5 1.5 0 0 1 21 7.5v12A1.5 1.5 0 0 1 19.5 21h-15A1.5 1.5 0 0 1 3 19.5v-12A1.5 1.5 0 0 1 4.5 6H6V4.5a1 1 0 0 1 1-1zM4.5 10v9.5h15V10h-15z"/>
                <path d="M7 13h3v3H7v-3zm5 0h3v3h-3v-3z" opacity=".9"/>
            </svg>
        @else
            <svg {{ $attributes->merge(['class' => $iconClass, 'data-mentea-icon' => $name]) }} fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3.5" y="5" width="17" height="15.5" rx="2.5"/>
                <path d="M3.5 10h17M8 3.5v4M16 3.5v4"/>
                <path d="M8 14h2.5M13.5 14H16" opacity=".7"/>
            </svg>
        @endif
        @break

    @case('clipboard')
        @if($isSolid)
            <svg {{ $attributes->merge(['class' => $iconClass, 'data-mentea-icon' => $name]) }} viewBox="0 0 24 24" fill="currentColor">
                <path d="M9 3.5h6a1.5 1.5 0 0 1 1.5 1.5v1H18a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h1.5V5A1.5 1.5 0 0 1 9 3.5zm0 3h6V5H9v1.5z"/>
                <path d="M8 12h8v1.5H8V12zm0 4h5v1.5H8V16z" fill="#fff" opacity=".9"/>
            </svg>
        @else
            <svg {{ $attributes->merge(['class' => $iconClass, 'data-mentea-icon' => $name]) }} fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <rect x="5" y="5" width="14" height="16" rx="2.5"/>
                <path d="M9 3.5h6v3H9v-3z"/>
                <path d="M8.5 12h7M8.5 16h5"/>
            </svg>
        @endif
        @break

    @case('clock')
        @if($isSolid)
            <svg {{ $attributes->merge(['class' => $iconClass, 'data-mentea-icon' => $name]) }} viewBox="0 0 24 24" fill="currentColor">
                <circle cx="12" cy="12" r="9"/>
                <path d="M12 7v5.2l3.5 2" stroke="#fff" stroke-width="1.8" stroke-linecap="round" fill="none"/>
            </svg>
        @else
            <svg {{ $attributes->merge(['class' => $iconClass, 'data-mentea-icon' => $name]) }} fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="8.5"/>
                <path d="M12 7.5v5l3.2 1.8"/>
            </svg>
        @endif
        @break

    @case('users')
        @if($isSolid)
            <svg {{ $attributes->merge(['class' => $iconClass, 'data-mentea-icon' => $name]) }} viewBox="0 0 24 24" fill="currentColor">
                <circle cx="9" cy="8" r="3.2"/>
                <path d="M2.5 19c0-2.8 2.9-5 6.5-5s6.5 2.2 6.5 5v.5h-13V19z"/>
                <circle cx="17" cy="9" r="2.6" opacity=".85"/>
                <path d="M15.5 19.5c.4-1.8 1.8-3.2 3.8-3.8 1.4.7 2.2 1.9 2.2 3.3v.5h-6v-.5z" opacity=".85"/>
            </svg>
        @else
            <svg {{ $attributes->merge(['class' => $iconClass, 'data-mentea-icon' => $name]) }} fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="9" cy="8" r="3"/>
                <path d="M3 19c1.2-2.8 3.5-4 6-4s4.8 1.2 6 4"/>
                <circle cx="17" cy="9" r="2.5"/>
                <path d="M15 19c.6-1.5 1.8-2.5 3.5-3"/>
            </svg>
        @endif
        @break

    @case('chart')
        @if($isSolid)
            <svg {{ $attributes->merge(['class' => $iconClass, 'data-mentea-icon' => $name]) }} viewBox="0 0 24 24" fill="currentColor">
                <path d="M4 19.5h2.5V10H4v9.5zm6.5 0H13V5.5h-2.5v14zm6.5 0H19.5v-7H17v7z"/>
            </svg>
        @else
            <svg {{ $attributes->merge(['class' => $iconClass, 'data-mentea-icon' => $name]) }} fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 19V10M12 19V5M19 19v-6"/>
            </svg>
        @endif
        @break

    @case('bell')
        @if($isSolid)
            <svg {{ $attributes->merge(['class' => $iconClass, 'data-mentea-icon' => $name]) }} viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2.5a1.5 1.5 0 0 1 1.5 1.5v.4A6.5 6.5 0 0 1 18.5 10.5v3.3c0 .4.1.8.4 1.1l1.3 1.3a1 1 0 0 1-.7 1.7H4.5a1 1 0 0 1-.7-1.7l1.3-1.3c.3-.3.4-.7.4-1.1v-3.3A6.5 6.5 0 0 1 10.5 4.4V4A1.5 1.5 0 0 1 12 2.5zM9.5 18.5a2.5 2.5 0 0 0 5 0h-5z"/>
            </svg>
        @else
            <svg {{ $attributes->merge(['class' => $iconClass, 'data-mentea-icon' => $name]) }} fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M7 17h10l-1.2-1.2A1.5 1.5 0 0 1 15.5 14.7V11a4.5 4.5 0 1 0-9 0v3.7c0 .4-.2.8-.5 1.1L5 17h2z"/>
                <path d="M10 17.5a2 2 0 0 0 4 0"/>
                <path d="M12 4.5V3.5"/>
            </svg>
        @endif
        @break

    @case('settings')
        @if($isSolid)
            <svg {{ $attributes->merge(['class' => $iconClass, 'data-mentea-icon' => $name]) }} viewBox="0 0 24 24" fill="currentColor">
                <path d="M10.1 2.8h3.8l.4 2a7.5 7.5 0 0 1 1.7 1l1.8-.7 1.9 3.3-1.5 1.2a7.5 7.5 0 0 1 0 2l1.5 1.2-1.9 3.3-1.8-.7a7.5 7.5 0 0 1-1.7 1l-.4 2h-3.8l-.4-2a7.5 7.5 0 0 1-1.7-1l-1.8.7L4.3 13l1.5-1.2a7.5 7.5 0 0 1 0-2L4.3 8.6l1.9-3.3 1.8.7a7.5 7.5 0 0 1 1.7-1l.4-2z"/>
                <circle cx="12" cy="12" r="2.8" fill="#fff"/>
            </svg>
        @else
            <svg {{ $attributes->merge(['class' => $iconClass, 'data-mentea-icon' => $name]) }} fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="3"/>
                <path d="M12 3.5v2M12 18.5v2M3.5 12h2M18.5 12h2M6.2 6.2l1.4 1.4M16.4 16.4l1.4 1.4M6.2 17.8l1.4-1.4M16.4 7.6l1.4-1.4"/>
            </svg>
        @endif
        @break

    @case('logout')
        @if($isSolid)
            <svg {{ $attributes->merge(['class' => $iconClass, 'data-mentea-icon' => $name]) }} viewBox="0 0 24 24" fill="currentColor">
                <path d="M4 5.5A1.5 1.5 0 0 1 5.5 4H12v2H6v12h6v2H5.5A1.5 1.5 0 0 1 4 18.5v-13z"/>
                <path d="M14 7l5 5-5 5v-3.5H9v-3h5V7z"/>
            </svg>
        @else
            <svg {{ $attributes->merge(['class' => $iconClass, 'data-mentea-icon' => $name]) }} fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10 4.5H6.5A2 2 0 0 0 4.5 6.5v11a2 2 0 0 0 2 2H10"/>
                <path d="M14 8l4 4-4 4M18 12H9"/>
            </svg>
        @endif
        @break

    @case('plus')
        @if($isSolid)
            <svg {{ $attributes->merge(['class' => $iconClass, 'data-mentea-icon' => $name]) }} viewBox="0 0 24 24" fill="currentColor">
                <path d="M11 5h2v6h6v2h-6v6h-2v-6H5v-2h6V5z"/>
            </svg>
        @else
            <svg {{ $attributes->merge(['class' => $iconClass, 'data-mentea-icon' => $name]) }} fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M12 6v12M6 12h12"/>
            </svg>
        @endif
        @break

    @case('heart')
        <svg {{ $attributes->merge(['class' => $iconClass, 'data-mentea-icon' => $name]) }} viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 20.4S4.5 15.5 2.8 11.2C1.5 8 3.2 5 6.5 5c1.9 0 3.2 1.1 4 2.2.8-1.1 2.1-2.2 4-2.2 3.3 0 5 3 3.7 6.2C16.5 15.5 12 20.4 12 20.4z"/>
        </svg>
        @break

    @case('chat')
        @if($isSolid)
            <svg {{ $attributes->merge(['class' => $iconClass, 'data-mentea-icon' => $name]) }} viewBox="0 0 24 24" fill="currentColor">
                <path d="M5 4.5h14A2.5 2.5 0 0 1 21.5 7v8A2.5 2.5 0 0 1 19 17.5h-5.2L8 21v-3.5H5A2.5 2.5 0 0 1 2.5 15V7A2.5 2.5 0 0 1 5 4.5z"/>
                <path d="M7.5 9.5h9v1.5h-9V9.5zm0 3.5h6v1.5h-6V13z" fill="#fff" opacity=".9"/>
            </svg>
        @else
            <svg {{ $attributes->merge(['class' => $iconClass, 'data-mentea-icon' => $name]) }} fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 5h14a1.5 1.5 0 0 1 1.5 1.5V15A1.5 1.5 0 0 1 19 16.5h-5L8 20v-3.5H5A1.5 1.5 0 0 1 3.5 15V6.5A1.5 1.5 0 0 1 5 5z"/>
                <path d="M8 9.5h8M8 13h5"/>
            </svg>
        @endif
        @break

    @case('list')
        @if($isSolid)
            <svg {{ $attributes->merge(['class' => $iconClass, 'data-mentea-icon' => $name]) }} viewBox="0 0 24 24" fill="currentColor">
                <circle cx="5" cy="7" r="1.5"/>
                <circle cx="5" cy="12" r="1.5"/>
                <circle cx="5" cy="17" r="1.5"/>
                <path d="M9 6h11v2H9V6zm0 5h11v2H9v-2zm0 5h11v2H9v-2z"/>
            </svg>
        @else
            <svg {{ $attributes->merge(['class' => $iconClass, 'data-mentea-icon' => $name]) }} fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                <path d="M9 7h11M9 12h11M9 17h11"/>
                <circle cx="5" cy="7" r="1" fill="currentColor"/>
                <circle cx="5" cy="12" r="1" fill="currentColor"/>
                <circle cx="5" cy="17" r="1" fill="currentColor"/>
            </svg>
        @endif
        @break

    @case('brain')
        {{-- Icono distintivo Mentea --}}
        <svg {{ $attributes->merge(['class' => $iconClass, 'data-mentea-icon' => $name]) }} fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 4.5c-2.5 0-4 1.8-4 4 0 1.2.5 2.2 1.3 2.9A3.5 3.5 0 0 0 5 14.5c0 2 1.6 3.5 3.5 3.5h1"/>
            <path d="M15 4.5c2.5 0 4 1.8 4 4 0 1.2-.5 2.2-1.3 2.9A3.5 3.5 0 0 1 19 14.5c0 2-1.6 3.5-3.5 3.5h-1"/>
            <path d="M9.5 8.5c1-.8 2.5-.8 3.5 0M9 12c1.2-.6 2.8-.6 4 0M10 15.5c.8-.4 2-.4 2.8 0"/>
            <path d="M12 18.5v2" opacity=".5"/>
        </svg>
        @break

    @case('lotus')
        <svg {{ $attributes->merge(['class' => $iconClass, 'data-mentea-icon' => $name]) }} fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 18c-2-3-5-5-8-5 2 0 5 1 8 5 3-4 6-5 8-5-3 0-6 2-8 5z"/>
            <path d="M12 18c-1-4-1-8 0-12 1 4 1 8 0 12z"/>
            <path d="M7 13c1.5-2 3.5-3.5 5-4 1.5.5 3.5 2 5 4"/>
        </svg>
        @break

    @default
        <svg {{ $attributes->merge(['class' => $iconClass, 'data-mentea-icon' => $name]) }} fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <circle cx="12" cy="12" r="8"/>
        </svg>
@endswitch
