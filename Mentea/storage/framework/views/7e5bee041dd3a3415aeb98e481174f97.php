<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name' => 'home',
    'active' => false,
    'size' => 'md', // sm | md | lg
]));

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

foreach (array_filter(([
    'name' => 'home',
    'active' => false,
    'size' => 'md', // sm | md | lg
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
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
?>

<span class="<?php echo \Illuminate\Support\Arr::toCssClasses([
    'mentea-nav-icon inline-flex items-center justify-center rounded-full transition-all duration-150',
    $sizeClass,
    'mentea-nav-icon--active bg-gradient-to-br from-teal-500 to-emerald-600 text-white shadow-sm' => $active,
    'bg-slate-100/90 text-slate-500 group-hover:bg-teal-50 group-hover:text-teal-700' => ! $active,
]); ?>">
    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => $name,'class' => $iconClass,'variant' => $active ? 'solid' : 'outline']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($name),'class' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($iconClass),'variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($active ? 'solid' : 'outline')]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
</span>
<?php /**PATH D:\OCTAVO\Proyecto Mentea\Mentea\resources\views/components/nav-icon.blade.php ENDPATH**/ ?>