<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="mentea-page-title">Detalle de auditoría #<?php echo e($log->id); ?></h2>
     <?php $__env->endSlot(); ?>
    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="mentea-card p-6 space-y-3 text-sm">
                <div><span class="text-slate-500">Acción:</span> <strong><?php echo e($log->action); ?></strong></div>
                <div><span class="text-slate-500">Usuario:</span> <?php echo e($log->user?->name ?? 'Sistema'); ?></div>
                <div><span class="text-slate-500">Fecha:</span> <?php echo e($log->created_at->format('d/m/Y H:i:s')); ?></div>
                <div><span class="text-slate-500">IP:</span> <?php echo e($log->ip_address ?? '—'); ?></div>
                <div><span class="text-slate-500">Agente:</span> <span class="text-xs break-all"><?php echo e($log->user_agent); ?></span></div>
                <?php if($log->old_values): ?>
                    <div>
                        <div class="text-slate-500 mb-1">Valores anteriores</div>
                        <pre class="bg-slate-50 p-3 rounded-lg text-xs overflow-auto"><?php echo e(json_encode($log->old_values, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)); ?></pre>
                    </div>
                <?php endif; ?>
                <?php if($log->new_values): ?>
                    <div>
                        <div class="text-slate-500 mb-1">Valores nuevos</div>
                        <pre class="bg-slate-50 p-3 rounded-lg text-xs overflow-auto"><?php echo e(json_encode($log->new_values, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)); ?></pre>
                    </div>
                <?php endif; ?>
            </div>
            <a href="<?php echo e(route('super.audit.index')); ?>" class="inline-block mt-4 text-sm text-slate-500 hover:underline">← Volver</a>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH D:\OCTAVO\Proyecto Mentea\Mentea\resources\views/super-admin/audit/show.blade.php ENDPATH**/ ?>