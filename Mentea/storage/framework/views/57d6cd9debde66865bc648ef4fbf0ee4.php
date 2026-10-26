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
        <h2 class="mentea-page-title">Panel super usuario</h2>
     <?php $__env->endSlot(); ?>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="mentea-stat">
                    <div class="mentea-stat-label">Tickets abiertos</div>
                    <div class="mentea-stat-value"><?php echo e($openTickets); ?></div>
                </div>
                <div class="mentea-stat">
                    <div class="mentea-stat-label">Auditoría hoy</div>
                    <div class="mentea-stat-value"><?php echo e($auditToday); ?></div>
                </div>
                <div class="mentea-stat">
                    <div class="mentea-stat-label">Ajustes</div>
                    <div class="mentea-stat-value"><?php echo e($settingsCount); ?></div>
                </div>
                <div class="mentea-stat">
                    <div class="mentea-stat-label">Usuarios</div>
                    <div class="mentea-stat-value"><?php echo e($usersCount); ?></div>
                </div>
            </div>

            <div class="grid lg:grid-cols-2 gap-6">
                <div class="mentea-card p-5">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="font-semibold text-slate-800">Tickets recientes</h3>
                        <a href="<?php echo e(route('super.tickets.index')); ?>" class="text-sm text-teal-700 hover:underline">Ver todos</a>
                    </div>
                    <ul class="divide-y text-sm">
                        <?php $__empty_1 = true; $__currentLoopData = $recentTickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <li class="py-2 flex justify-between gap-2">
                                <a href="<?php echo e(route('super.tickets.show', $t)); ?>" class="text-teal-700 hover:underline truncate"><?php echo e($t->subject); ?></a>
                                <span class="text-slate-400 shrink-0"><?php echo e($t->statusLabel()); ?></span>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <li class="py-4 text-slate-500">Sin tickets.</li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="mentea-card p-5">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="font-semibold text-slate-800">Auditoría reciente</h3>
                        <a href="<?php echo e(route('super.audit.index')); ?>" class="text-sm text-teal-700 hover:underline">Ver todo</a>
                    </div>
                    <ul class="divide-y text-sm">
                        <?php $__empty_1 = true; $__currentLoopData = $recentAudit; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <li class="py-2">
                                <div class="font-medium text-slate-700"><?php echo e($log->action); ?></div>
                                <div class="text-xs text-slate-400"><?php echo e($log->user?->name ?? 'Sistema'); ?> · <?php echo e($log->created_at->diffForHumans()); ?></div>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <li class="py-4 text-slate-500">Sin registros.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="<?php echo e(route('super.settings.index')); ?>" class="btn-mentea">Configuración del sistema</a>
                <a href="<?php echo e(route('super.tickets.index')); ?>" class="btn-mentea-outline">Tickets de soporte</a>
                <a href="<?php echo e(route('super.audit.index')); ?>" class="btn-mentea-outline">Auditoría</a>
            </div>
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
<?php /**PATH D:\OCTAVO\Proyecto Mentea\Mentea\resources\views/super-admin/dashboard.blade.php ENDPATH**/ ?>