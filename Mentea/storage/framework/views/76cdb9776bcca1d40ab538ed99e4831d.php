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
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Citas</h2>
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="text-sm text-teal-600 hover:underline">Dashboard</a>
        </div>
     <?php $__env->endSlot(); ?>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <?php if(session('success')): ?>
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-lg text-sm"><?php echo e(session('success')); ?></div>
            <?php endif; ?>
            <form method="GET" class="mb-4 flex flex-wrap gap-2 items-end">
                <div>
                    <label class="text-xs text-gray-500">Buscar paciente</label>
                    <input type="text" name="q" value="<?php echo e(request('q')); ?>" class="border-gray-300 rounded-lg text-sm block">
                </div>
                <div>
                    <label class="text-xs text-gray-500">Estado</label>
                    <select name="status" class="border-gray-300 rounded-lg text-sm block">
                        <option value="">Todos</option>
                        <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($st->value); ?>" <?php if(request('status')===$st->value): echo 'selected'; endif; ?>><?php echo e($st->label()); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500">Profesional</label>
                    <select name="professional_id" class="border-gray-300 rounded-lg text-sm block">
                        <option value="">Todos</option>
                        <?php $__currentLoopData = $professionals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pro): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($pro->id); ?>" <?php if(request('professional_id')==$pro->id): echo 'selected'; endif; ?>><?php echo e($pro->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500">Desde</label>
                    <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>" class="border-gray-300 rounded-lg text-sm block">
                </div>
                <div>
                    <label class="text-xs text-gray-500">Hasta</label>
                    <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>" class="border-gray-300 rounded-lg text-sm block">
                </div>
                <button class="px-3 py-1.5 bg-gray-800 text-white text-sm rounded-lg">Filtrar</button>
            </form>
            <div class="bg-white rounded-xl shadow-sm border overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left">Fecha</th>
                            <th class="px-4 py-3 text-left">Paciente</th>
                            <th class="px-4 py-3 text-left">Profesional</th>
                            <th class="px-4 py-3 text-left">Modalidad</th>
                            <th class="px-4 py-3 text-left">Estado</th>
                            <th class="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php $__empty_1 = true; $__currentLoopData = $appointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cita): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <?php echo e($cita->date->format('d/m/Y')); ?>

                                    <span class="text-gray-500"><?php echo e(\Carbon\Carbon::parse($cita->time)->format('H:i')); ?></span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium"><?php echo e($cita->patient->name ?? '—'); ?></div>
                                    <div class="text-xs text-gray-500"><?php echo e($cita->patient->document_number ?? ''); ?></div>
                                </td>
                                <td class="px-4 py-3"><?php echo e($cita->professional->name ?? '—'); ?></td>
                                <td class="px-4 py-3"><?php echo e($cita->modality->label()); ?></td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 text-xs rounded-full bg-<?php echo e($cita->status->color()); ?>-100 text-<?php echo e($cita->status->color()); ?>-800">
                                        <?php echo e($cita->status->label()); ?>

                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="<?php echo e(route('admin.appointments.show', $cita)); ?>" class="text-teal-600 hover:underline">Gestionar</a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">No hay citas con estos filtros.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-4"><?php echo e($appointments->links()); ?></div>
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
<?php /**PATH D:\OCTAVO\Proyecto Mentea\Mentea\resources\views/admin/appointments/index.blade.php ENDPATH**/ ?>