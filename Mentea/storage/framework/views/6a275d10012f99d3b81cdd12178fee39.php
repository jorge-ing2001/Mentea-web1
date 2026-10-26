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
            <h2 class="mentea-page-title">Tickets y diligenciamientos</h2>
            <a href="<?php echo e(route('super.tickets.create')); ?>" class="btn-mentea text-sm">+ Evento plataforma</a>
        </div>
     <?php $__env->endSlot(); ?>
    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <?php if(session('success')): ?>
                <div class="alert-success"><?php echo e(session('success')); ?></div>
            <?php endif; ?>
            <p class="text-sm text-slate-600">
                <strong>Soporte:</strong> solicitudes de usuarios.
                <strong>Diligenciamiento:</strong> eventos documentados por el equipo sobre la plataforma.
            </p>
            <form method="GET" class="flex gap-2 flex-wrap">
                <select name="type" class="mentea-input w-auto" onchange="this.form.submit()">
                    <option value="">Todos los tipos</option>
                    <option value="support" <?php if(($type ?? '')==='support'): echo 'selected'; endif; ?>>Soporte usuario</option>
                    <option value="platform_event" <?php if(($type ?? '')==='platform_event'): echo 'selected'; endif; ?>>Diligenciamiento / evento</option>
                </select>
                <select name="status" class="mentea-input w-auto" onchange="this.form.submit()">
                    <option value="">Todos los estados</option>
                    <?php $__currentLoopData = ['open'=>'Abierto','in_progress'=>'En progreso','resolved'=>'Resuelto','closed'=>'Cerrado']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v=>$l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($v); ?>" <?php if(($status ?? '')===$v): echo 'selected'; endif; ?>><?php echo e($l); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </form>
            <div class="mentea-card overflow-hidden">
                <table class="mentea-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tipo</th>
                            <th>Asunto</th>
                            <th>Usuario</th>
                            <th>Prioridad</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($t->id); ?></td>
                                <td>
                                    <span class="text-xs px-2 py-0.5 rounded-full <?php echo e($t->isPlatformEvent() ? 'bg-amber-50 text-amber-800' : 'bg-teal-50 text-teal-800'); ?>">
                                        <?php echo e($t->typeLabel()); ?>

                                    </span>
                                </td>
                                <td><a href="<?php echo e(route('super.tickets.show', $t)); ?>" class="text-teal-700 hover:underline"><?php echo e($t->subject); ?></a></td>
                                <td><?php echo e($t->user?->name); ?></td>
                                <td><?php echo e($t->priorityLabel()); ?></td>
                                <td><?php echo e($t->statusLabel()); ?></td>
                                <td class="text-slate-500"><?php echo e($t->created_at->format('d/m/Y H:i')); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="7" class="text-center text-slate-500 py-8">No hay registros.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php echo e($tickets->links()); ?>

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
<?php /**PATH D:\OCTAVO\Proyecto Mentea\Mentea\resources\views/super-admin/tickets/index.blade.php ENDPATH**/ ?>