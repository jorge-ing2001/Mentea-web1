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
            <h2 class="mentea-page-title">Diligenciamientos de plataforma</h2>
            <a href="<?php echo e(route('admin.events.create')); ?>" class="btn-mentea text-sm">Registrar evento</a>
        </div>
     <?php $__env->endSlot(); ?>
    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <?php if(session('success')): ?>
                <div class="alert-success"><?php echo e(session('success')); ?></div>
            <?php endif; ?>
            <p class="text-sm text-slate-600">
                Aquí el administrador documenta <strong>eventos e incidencias</strong> de la plataforma
                (mantenimiento, cambios, seguridad, etc.). No son tickets de pacientes.
            </p>
            <div class="mentea-card overflow-hidden">
                <table class="mentea-table">
                    <thead>
                        <tr>
                            <th>Fecha evento</th>
                            <th>Categoría</th>
                            <th>Asunto</th>
                            <th>Registrado por</th>
                            <th>Prioridad</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="whitespace-nowrap text-slate-500"><?php echo e($e->event_date?->format('d/m/Y H:i') ?? $e->created_at->format('d/m/Y H:i')); ?></td>
                                <td><?php echo e(\App\Models\SupportTicket::categories()[$e->category] ?? $e->category); ?></td>
                                <td>
                                    <div class="font-medium"><?php echo e($e->subject); ?></div>
                                    <div class="text-xs text-slate-500 line-clamp-2"><?php echo e(\Illuminate\Support\Str::limit($e->message, 100)); ?></div>
                                </td>
                                <td><?php echo e($e->user?->name); ?></td>
                                <td><?php echo e($e->priorityLabel()); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="5" class="text-center py-8 text-slate-500">Sin diligenciamientos aún.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php echo e($events->links()); ?>

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
<?php /**PATH D:\OCTAVO\Proyecto Mentea\Mentea\resources\views/admin/events/index.blade.php ENDPATH**/ ?>