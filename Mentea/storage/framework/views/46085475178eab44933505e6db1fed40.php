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
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Lista de espera</h2>
     <?php $__env->endSlot(); ?>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <?php if(session('success')): ?>
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-lg text-sm"><?php echo e(session('success')); ?></div>
            <?php endif; ?>
            <form method="GET" class="mb-4">
                <select name="status" onchange="this.form.submit()" class="border-gray-300 rounded-lg text-sm">
                    <option value="">Todos</option>
                    <?php $__currentLoopData = ['waiting','notified','assigned','cancelled']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($st); ?>" <?php if(request('status')===$st): echo 'selected'; endif; ?>><?php echo e($st); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </form>
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <table class="min-w-full divide-y text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left">Paciente</th>
                            <th class="px-4 py-3 text-left">Preferencia</th>
                            <th class="px-4 py-3 text-left">Fecha pref.</th>
                            <th class="px-4 py-3 text-left">Estado</th>
                            <th class="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php $__empty_1 = true; $__currentLoopData = $entries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-medium"><?php echo e($e->patient->name ?? '—'); ?></div>
                                    <div class="text-xs text-gray-500"><?php echo e($e->patient->email ?? ''); ?></div>
                                </td>
                                <td class="px-4 py-3">
                                    <?php echo e($e->specialty ?? '—'); ?>

                                    <?php if($e->professional): ?><br><span class="text-xs text-gray-500"><?php echo e($e->professional->name); ?></span><?php endif; ?>
                                </td>
                                <td class="px-4 py-3"><?php echo e($e->preferred_date?->format('d/m/Y') ?? '—'); ?></td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100"><?php echo e($e->status); ?></span>
                                    <?php if($e->assigned_at): ?>
                                        <div class="text-xs text-gray-500 mt-1">Asignada <?php echo e($e->assigned_at->format('d/m/Y H:i')); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    <?php if(in_array($e->status, ['waiting', 'notified'])): ?>
                                        <a href="<?php echo e(route('admin.waitlist.assign', $e)); ?>" class="text-teal-600 hover:underline font-medium">Asignar cita</a>
                                    <?php endif; ?>
                                    <?php if($e->assigned_appointment_id): ?>
                                        <a href="<?php echo e(route('admin.appointments.show', $e->assigned_appointment_id)); ?>" class="text-gray-600 hover:underline">Ver cita</a>
                                    <?php endif; ?>
                                    <form method="POST" action="<?php echo e(route('admin.waitlist.status', $e)); ?>" class="inline-flex gap-1 items-center">
                                        <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                        <select name="status" class="border-gray-300 rounded text-xs">
                                            <?php $__currentLoopData = ['waiting','notified','assigned','cancelled']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($st); ?>" <?php if($e->status===$st): echo 'selected'; endif; ?>><?php echo e($st); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <button class="text-teal-600 text-xs hover:underline">OK</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Sin solicitudes.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-4"><?php echo e($entries->links()); ?></div>
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
<?php /**PATH D:\OCTAVO\Proyecto Mentea\Mentea\resources\views/admin/waitlist/index.blade.php ENDPATH**/ ?>