<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['variant' => 'symbol']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['variant' => 'symbol']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php if($variant === 'horizontal'): ?>
    <img src="<?php echo e(asset('images/brand/logo-horizontal-claro.png')); ?>" alt="Mentea" <?php echo e($attributes->merge(['class' => 'h-10 w-auto object-contain'])); ?>>
<?php elseif($variant === 'dark'): ?>
    <img src="<?php echo e(asset('images/brand/logo-principal-oscuro.png')); ?>" alt="Mentea" <?php echo e($attributes->merge(['class' => 'h-16 w-auto object-contain'])); ?>>
<?php elseif($variant === 'full'): ?>
    <img src="<?php echo e(asset('images/brand/logo-principal-claro.png')); ?>" alt="Mentea — Conecta con tu bienestar mental" <?php echo e($attributes->merge(['class' => 'h-20 w-auto object-contain'])); ?>>
<?php else: ?>
    <img src="<?php echo e(asset('images/brand/logo-symbol.svg')); ?>" alt="Mentea" <?php echo e($attributes->merge(['class' => 'h-9 w-9 object-contain'])); ?>

         onerror="this.src='<?php echo e(asset('images/brand/logo-symbol.png')); ?>'">
<?php endif; ?>
<?php /**PATH D:\OCTAVO\Proyecto Mentea\Mentea\resources\views/components/application-logo.blade.php ENDPATH**/ ?>