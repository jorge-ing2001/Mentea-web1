@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-teal-600 text-start text-base font-medium text-teal-700 bg-teal-50 focus:outline-none transition'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-slate-600 hover:text-teal-700 hover:bg-teal-50 hover:border-teal-300 focus:outline-none transition';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    <span class="inline-flex items-center gap-2">{{ $slot }}</span>
</a>
