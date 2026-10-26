@props(['active'])

@php
$classes = ($active ?? false)
            ? 'mentea-nav-link-active group'
            : 'mentea-nav-link group';
@endphp

<a {{ $attributes->merge(['class' => $classes . ' gap-1.5']) }}>
    {{ $slot }}
</a>
