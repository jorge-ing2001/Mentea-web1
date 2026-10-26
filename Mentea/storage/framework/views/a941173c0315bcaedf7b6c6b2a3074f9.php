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
        <div class="flex justify-between items-center flex-wrap gap-2">
            <h2 class="mentea-page-title">Horarios excepcionales</h2>
            <a href="<?php echo e(route('admin.exceptions.create')); ?>" class="btn-mentea text-sm">+ Nueva excepción</a>
        </div>
     <?php $__env->endSlot(); ?>
    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <?php if(session('success')): ?><div class="alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>
            <p class="text-sm text-slate-600">Festivos, ausencias y bloqueos puntuales. No generan citas disponibles.</p>
            <div class="mentea-card overflow-hidden">
                <table class="mentea-table">
                    <thead>
                        <tr><th>Fecha</th><th>Tipo</th><th>Profesional</th><th>Horario</th><th>Motivo</th><th></th></tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $exceptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ex): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($ex->date->format('d/m/Y')); ?></td>
                                <td><?php echo e($ex->typeLabel()); ?></td>
                                <td><?php echo e($ex->professional?->name ?? 'Toda la clínica'); ?></td>
                                <td>
                                    <?php if($ex->isFullDay()): ?> Día completo
                                    <?php else: ?> <?php echo e(\Carbon\Carbon::parse($ex->time_from)->format('H:i')); ?> – <?php echo e(\Carbon\Carbon::parse($ex->time_to)->format('H:i')); ?>

                                    <?php endif; ?>
                                </td>
                                <td class="text-sm text-slate-500"><?php echo e($ex->reason); ?></td>
                                <td>
                                    <form method="POST" action="<?php echo e(route('admin.exceptions.destroy', $ex)); ?>" onsubmit="return confirm('¿Eliminar?')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button class="text-red-600 text-sm hover:underline">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="6" class="text-center py-8 text-slate-500">Sin excepciones.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php echo e($exceptions->links()); ?>

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
<?php /**PATH D:\OCTAVO\Proyecto Mentea\Mentea\resources\views/admin/exceptions/index.blade.php ENDPATH**/ ?>