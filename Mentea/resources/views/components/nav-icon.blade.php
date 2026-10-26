@props([
    'name' => 'home',
    'active' => false,
    'size' => 'md', // sm | md | lg
])

@php
$sizeClass = match($size) {
    'sm' => 'w-6 h-6',
    'lg' => 'w-9 h-9',
    default => 'w-7 h-7',
};
$iconClass = match($size) {
    'sm' => 'w-3.5 h-3.5',
    'lg' => 'w-5 h-5',
    default => 'w-4 h-4',
};
@endphp

<span @class([
    'mentea-nav-icon inline-flex items-center justify-center rounded-full transition-all duration-150',
    $sizeClass,
    'mentea-nav-icon--active bg-gradient-to-br from-teal-500 to-emerald-600 text-white shadow-sm' => $active,
    'bg-slate-100/90 text-slate-500 group-hover:bg-teal-50 group-hover:text-teal-700' => ! $active,
])>
    <x-icon :name="$name" :class="$iconClass" :variant="$active ? 'solid' : 'outline'" />
</span>
