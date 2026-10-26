<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['appointment']));

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

foreach (array_filter((['appointment']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php if($appointment->order_document || $appointment->authorization_document): ?>
<div class="border rounded-xl p-4 space-y-3 bg-slate-50">
    <h4 class="font-medium text-sm text-gray-800">Documentos adjuntos</h4>
    <div class="grid sm:grid-cols-2 gap-3">
        <div class="bg-white border rounded-lg p-3">
            <div class="text-xs font-semibold text-teal-700 uppercase tracking-wide mb-2">Orden</div>
            <?php if($appointment->order_document): ?>
                <div class="flex flex-wrap gap-2">
                    <a href="<?php echo e(route('documents.view', ['appointment' => $appointment, 'type' => 'order'])); ?>"
                       target="_blank" rel="noopener"
                       class="inline-flex items-center px-3 py-1.5 text-sm rounded-lg bg-teal-600 text-white hover:bg-teal-700">
                        Ver documento
                    </a>
                    <a href="<?php echo e(route('documents.download', ['appointment' => $appointment, 'type' => 'order'])); ?>"
                       class="inline-flex items-center px-3 py-1.5 text-sm rounded-lg border border-gray-300 hover:bg-gray-50">
                        Descargar
                    </a>
                </div>
            <?php else: ?>
                <p class="text-sm text-gray-400">No adjuntada</p>
            <?php endif; ?>
        </div>
        <div class="bg-white border rounded-lg p-3">
            <div class="text-xs font-semibold text-emerald-700 uppercase tracking-wide mb-2">Autorización</div>
            <?php if($appointment->authorization_document): ?>
                <div class="flex flex-wrap gap-2">
                    <a href="<?php echo e(route('documents.view', ['appointment' => $appointment, 'type' => 'authorization'])); ?>"
                       target="_blank" rel="noopener"
                       class="inline-flex items-center px-3 py-1.5 text-sm rounded-lg bg-emerald-600 text-white hover:bg-emerald-700">
                        Ver documento
                    </a>
                    <a href="<?php echo e(route('documents.download', ['appointment' => $appointment, 'type' => 'authorization'])); ?>"
                       class="inline-flex items-center px-3 py-1.5 text-sm rounded-lg border border-gray-300 hover:bg-gray-50">
                        Descargar
                    </a>
                </div>
            <?php else: ?>
                <p class="text-sm text-gray-400">No adjuntada</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>
<?php /**PATH D:\OCTAVO\Proyecto Mentea\Mentea\resources\views/components/appointment-documents.blade.php ENDPATH**/ ?>