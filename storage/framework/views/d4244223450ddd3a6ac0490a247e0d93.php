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
     <?php $__env->slot('header', null, []); ?> <h2 class="mentea-page-title">Encuestas de satisfacción</h2> <?php $__env->endSlot(); ?>
    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid sm:grid-cols-3 gap-4">
                <div class="mentea-stat"><div class="mentea-stat-label">Respuestas</div><div class="mentea-stat-value"><?php echo e($count); ?></div></div>
                <div class="mentea-stat"><div class="mentea-stat-label">Promedio</div><div class="mentea-stat-value"><?php echo e($avg ? number_format($avg, 1) : '—'); ?> / 5</div></div>
                <div class="mentea-stat"><div class="mentea-stat-label">Pendientes (30 d)</div><div class="mentea-stat-value"><?php echo e($pending); ?></div></div>
            </div>
            <div class="mentea-card overflow-hidden">
                <table class="mentea-table">
                    <thead>
                        <tr><th>Fecha</th><th>Paciente</th><th>Profesional</th><th>Nota</th><th>Comentario</th></tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $surveys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="text-slate-500"><?php echo e($s->created_at->format('d/m/Y')); ?></td>
                                <td><?php echo e($s->patient?->name); ?></td>
                                <td><?php echo e($s->appointment?->professional?->name); ?></td>
                                <td><strong><?php echo e($s->rating); ?></strong> ★</td>
                                <td class="text-sm text-slate-600"><?php echo e($s->comment); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="5" class="text-center py-8 text-slate-500">Sin encuestas aún.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php echo e($surveys->links()); ?>

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
<?php /**PATH D:\OCTAVO\Proyecto Mentea\Mentea\resources\views/admin/surveys/index.blade.php ENDPATH**/ ?>