@props(['value'])

<label {{ $attributes->merge(['class' => 'mentea-label']) }}>
    {{ $value ?? $slot }}
</label>
