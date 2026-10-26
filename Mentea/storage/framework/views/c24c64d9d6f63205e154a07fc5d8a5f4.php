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
     <?php $__env->slot('header', null, []); ?> <h2 class="mentea-page-title">Reportes</h2> <?php $__env->endSlot(); ?>
    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <form method="GET" class="flex flex-wrap gap-2 items-end">
                <div>
                    <label class="mentea-label">Desde</label>
                    <input type="date" name="from" value="<?php echo e($from->toDateString()); ?>" class="mentea-input">
                </div>
                <div>
                    <label class="mentea-label">Hasta</label>
                    <input type="date" name="to" value="<?php echo e($to->toDateString()); ?>" class="mentea-input">
                </div>
                <button class="btn-mentea">Aplicar</button>
                <a href="<?php echo e(route('admin.reports.export', request()->query())); ?>" class="btn-mentea-outline text-sm">Exportar CSV</a>
            </form>

            <div class="grid sm:grid-cols-3 gap-4">
                <div class="mentea-stat"><div class="mentea-stat-label">Encuestas (NPS-ish)</div><div class="mentea-stat-value"><?php echo e($nps['count']); ?></div></div>
                <div class="mentea-stat"><div class="mentea-stat-label">Promedio ★</div><div class="mentea-stat-value"><?php echo e($nps['avg'] ?: '—'); ?></div></div>
                <div class="mentea-stat"><div class="mentea-stat-label">Pacientes en riesgo</div><div class="mentea-stat-value"><?php echo e($atRisk->count()); ?></div></div>
            </div>

            <div class="mentea-card p-5">
                <h3 class="font-semibold mb-3">Ocupación por profesional</h3>
                <table class="mentea-table">
                    <thead><tr><th>Profesional</th><th>Total</th><th>Completadas</th><th>No-show</th></tr></thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $byProfessional; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($row['name']); ?></td>
                                <td><?php echo e($row['total']); ?></td>
                                <td><?php echo e($row['completed']); ?></td>
                                <td><?php echo e($row['no_show']); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="4" class="text-center text-slate-500 py-6">Sin datos en el rango.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="mentea-card p-5">
                <h3 class="font-semibold mb-3">Pacientes en riesgo</h3>
                <p class="text-xs text-slate-500 mb-3">≥2 no-shows, ≥3 cancelaciones o bloqueo activo.</p>
                <table class="mentea-table">
                    <thead><tr><th>Paciente</th><th>No-shows</th><th>Cancelaciones</th><th>Bloqueo</th></tr></thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $atRisk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($p->name); ?><div class="text-xs text-slate-400"><?php echo e($p->email); ?></div></td>
                                <td><?php echo e($p->no_show_count); ?></td>
                                <td><?php echo e($p->cancellation_count); ?></td>
                                <td class="text-sm"><?php echo e($p->booking_blocked_until?->format('d/m/Y H:i') ?? '—'); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="4" class="text-center text-slate-500 py-6">Ninguno.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
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
<?php /**PATH D:\OCTAVO\Proyecto Mentea\Mentea\resources\views/admin/reports/index.blade.php ENDPATH**/ ?>