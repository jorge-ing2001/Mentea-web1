@props(['variant' => 'symbol'])

@if($variant === 'horizontal')
    <img src="{{ asset('images/brand/logo-horizontal-claro.png') }}" alt="Mentea" {{ $attributes->merge(['class' => 'h-10 w-auto object-contain']) }}>
@elseif($variant === 'dark')
    <img src="{{ asset('images/brand/logo-principal-oscuro.png') }}" alt="Mentea" {{ $attributes->merge(['class' => 'h-16 w-auto object-contain']) }}>
@elseif($variant === 'full')
    <img src="{{ asset('images/brand/logo-principal-claro.png') }}" alt="Mentea — Conecta con tu bienestar mental" {{ $attributes->merge(['class' => 'h-20 w-auto object-contain']) }}>
@else
    <img src="{{ asset('images/brand/logo-symbol.svg') }}" alt="Mentea" {{ $attributes->merge(['class' => 'h-9 w-9 object-contain']) }}
         onerror="this.src='{{ asset('images/brand/logo-symbol.png') }}'">
@endif
