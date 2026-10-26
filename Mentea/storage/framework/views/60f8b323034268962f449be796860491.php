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
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Panel Administrador — Mentea</h2>
            <div class="flex flex-wrap gap-2">
                <a href="<?php echo e(route('admin.professionals.index')); ?>" class="text-sm px-3 py-1.5 bg-white border rounded-lg hover:bg-gray-50">Profesionales</a>
                <a href="<?php echo e(route('admin.appointments.index')); ?>" class="text-sm px-3 py-1.5 bg-white border rounded-lg hover:bg-gray-50">Citas</a>
                <a href="<?php echo e(route('admin.calendar.index')); ?>" class="text-sm px-3 py-1.5 bg-white border rounded-lg hover:bg-gray-50">Calendario</a>
                <a href="<?php echo e(route('admin.waitlist.index')); ?>" class="text-sm px-3 py-1.5 bg-white border rounded-lg hover:bg-gray-50">Lista de espera</a>
            </div>
        </div>
     <?php $__env->endSlot(); ?>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl shadow-sm border p-5">
                    <div class="text-sm text-gray-500">Pacientes</div>
                    <div class="text-3xl font-bold text-teal-600"><?php echo e($totalPatients); ?></div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border p-5">
                    <div class="text-sm text-gray-500">Profesionales activos</div>
                    <div class="text-3xl font-bold text-teal-600"><?php echo e($totalProfessionals); ?></div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border p-5">
                    <div class="text-sm text-gray-500">Total citas</div>
                    <div class="text-3xl font-bold text-teal-600"><?php echo e($totalAppointments); ?></div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border p-5">
                    <div class="text-sm text-gray-500">Citas hoy</div>
                    <div class="text-3xl font-bold text-teal-600"><?php echo e($appointmentsToday); ?></div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="bg-white rounded-xl shadow-sm border p-5">
                    <h3 class="font-medium mb-3">Por estado</h3>
                    <ul class="space-y-2 text-sm">
                        <?php $__currentLoopData = ['pending'=>'Pendiente','confirmed'=>'Confirmada','cancelled'=>'Cancelada','completed'=>'Completada','no_show'=>'No asistió']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="flex justify-between">
                                <span><?php echo e($label); ?></span>
                                <span class="font-semibold"><?php echo e($byStatus[$key] ?? 0); ?></span>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border p-5">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="font-medium">Próximas citas</h3>
                        <a href="<?php echo e(route('admin.appointments.index')); ?>" class="text-sm text-teal-600 hover:underline">Ver todas</a>
                    </div>
                    <?php $__empty_1 = true; $__currentLoopData = $upcoming; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cita): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <a href="<?php echo e(route('admin.appointments.show', $cita)); ?>" class="border-b py-2 flex justify-between text-sm hover:bg-gray-50 block px-1">
                            <div>
                                <span class="font-medium"><?php echo e($cita->patient->name ?? '—'); ?></span>
                                <span class="text-gray-500"> · <?php echo e($cita->professional->name ?? ''); ?></span>
                            </div>
                            <div class="text-gray-600">
                                <?php echo e($cita->date->format('d/m')); ?> <?php echo e(\Carbon\Carbon::parse($cita->time)->format('H:i')); ?>

                            </div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-gray-500 text-sm">No hay citas próximas. <a href="<?php echo e(route('admin.professionals.index')); ?>" class="text-teal-600">Gestionar profesionales</a></p>
                    <?php endif; ?>
                </div>
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
<?php /**PATH D:\OCTAVO\Proyecto Mentea\Mentea\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>