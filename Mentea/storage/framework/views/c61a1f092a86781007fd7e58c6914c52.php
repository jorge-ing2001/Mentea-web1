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
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Horarios — <?php echo e($professional->name); ?></h2>
     <?php $__env->endSlot(); ?>
    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <?php if(session('success')): ?>
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-lg text-sm"><?php echo e(session('success')); ?></div>
            <?php endif; ?>
            <form method="POST" action="<?php echo e(route('admin.schedules.update', $professional)); ?>" class="bg-white rounded-xl shadow-sm border p-6 space-y-4">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <p class="text-sm text-gray-500 mb-4">Define los bloques horarios. Se eliminarán los actuales y se crearán los nuevos.</p>
                <?php
                    $existing = $professional->schedules->groupBy('day_of_week');
                    $idx = 0;
                ?>
                <?php $__currentLoopData = $days; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $num => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $daySchedules = $existing->get($num, collect()); ?>
                    <div class="border rounded-lg p-4">
                        <h4 class="font-medium mb-2"><?php echo e($label); ?></h4>
                        <?php $__empty_1 = true; $__currentLoopData = $daySchedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="flex gap-2 items-center mb-2">
                                <input type="hidden" name="schedules[<?php echo e($idx); ?>][day_of_week]" value="<?php echo e($num); ?>">
                                <input type="time" name="schedules[<?php echo e($idx); ?>][start_time]" value="<?php echo e(\Carbon\Carbon::parse($sch->start_time)->format('H:i')); ?>" class="border-gray-300 rounded-lg text-sm">
                                <span>—</span>
                                <input type="time" name="schedules[<?php echo e($idx); ?>][end_time]" value="<?php echo e(\Carbon\Carbon::parse($sch->end_time)->format('H:i')); ?>" class="border-gray-300 rounded-lg text-sm">
                                <label class="flex items-center gap-1 text-sm">
                                    <input type="checkbox" name="schedules[<?php echo e($idx); ?>][is_active]" value="1" <?php if($sch->is_active): echo 'checked'; endif; ?> class="rounded">
                                    Activo
                                </label>
                            </div>
                            <?php $idx++; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="flex gap-2 items-center mb-2">
                                <input type="hidden" name="schedules[<?php echo e($idx); ?>][day_of_week]" value="<?php echo e($num); ?>">
                                <input type="time" name="schedules[<?php echo e($idx); ?>][start_time]" class="border-gray-300 rounded-lg text-sm">
                                <span>—</span>
                                <input type="time" name="schedules[<?php echo e($idx); ?>][end_time]" class="border-gray-300 rounded-lg text-sm">
                                <label class="flex items-center gap-1 text-sm">
                                    <input type="checkbox" name="schedules[<?php echo e($idx); ?>][is_active]" value="1" checked class="rounded">
                                    Activo
                                </label>
                            </div>
                            <?php $idx++; ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <div class="flex gap-3 pt-2">
                    <a href="<?php echo e(route('admin.professionals.edit', $professional)); ?>" class="px-4 py-2 border rounded-lg">Volver</a>
                    <button type="submit" class="flex-1 bg-teal-600 text-white py-2 rounded-lg hover:bg-teal-700">Guardar horarios</button>
                </div>
            </form>
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
<?php /**PATH D:\OCTAVO\Proyecto Mentea\Mentea\resources\views/admin/schedules/edit.blade.php ENDPATH**/ ?>