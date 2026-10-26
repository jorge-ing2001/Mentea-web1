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
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Agenda — Mentea</h2>
            <?php if($professional): ?>
                <a href="<?php echo e(route('professional.appointments.index')); ?>" class="text-sm px-3 py-1.5 bg-white border rounded-lg hover:bg-gray-50">Todas mis citas</a>
                <a href="<?php echo e(route('professional.calendar.index')); ?>" class="text-sm px-3 py-1.5 bg-white border rounded-lg hover:bg-gray-50">Calendario</a>
            <?php endif; ?>
        </div>
     <?php $__env->endSlot(); ?>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <?php if(!$professional): ?>
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-6 text-amber-800">
                    Tu cuenta aún no está vinculada a un perfil profesional. Contacta al administrador.
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white rounded-xl shadow-sm border p-5">
                        <div class="text-sm text-gray-500">Citas hoy</div>
                        <div class="text-3xl font-bold text-teal-600"><?php echo e($todayAppointments->count()); ?></div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border p-5">
                        <div class="text-sm text-gray-500">Esta semana</div>
                        <div class="text-3xl font-bold text-teal-600"><?php echo e($weekCount); ?></div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border p-5">
                        <div class="text-sm text-gray-500">Pendientes de confirmar</div>
                        <div class="text-3xl font-bold text-amber-600"><?php echo e($pendingCount); ?></div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border p-6">
                    <h3 class="font-medium mb-4">Citas de hoy — <?php echo e($professional->name); ?></h3>
                    <?php $__empty_1 = true; $__currentLoopData = $todayAppointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cita): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="border-b py-3 flex justify-between items-center">
                            <div>
                                <div class="font-medium"><?php echo e($cita->patient->name); ?></div>
                                <div class="text-sm text-gray-500">
                                    <?php echo e(\Carbon\Carbon::parse($cita->time)->format('H:i')); ?> · <?php echo e($cita->modality->label()); ?>

                                    <?php if($cita->patient->phone): ?> · <?php echo e($cita->patient->phone); ?> <?php endif; ?>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 text-xs rounded-full bg-<?php echo e($cita->status->color()); ?>-100 text-<?php echo e($cita->status->color()); ?>-800">
                                    <?php echo e($cita->status->label()); ?>

                                </span>
                                <a href="<?php echo e(route('professional.appointments.show', $cita)); ?>" class="text-sm text-teal-600 hover:underline">Ver</a>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-gray-500 text-sm">No tienes citas programadas para hoy.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
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
<?php /**PATH D:\OCTAVO\Proyecto Mentea\Mentea\resources\views/professional/dashboard.blade.php ENDPATH**/ ?>