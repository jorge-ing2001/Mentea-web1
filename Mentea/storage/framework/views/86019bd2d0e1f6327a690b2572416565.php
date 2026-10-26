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
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Hola, <?php echo e(Auth::user()->name); ?></h2>
            <div class="flex gap-2">
                <a href="<?php echo e(route('patient.appointments.create')); ?>" class="bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium px-4 py-2 rounded-lg">+ Agendar cita</a>
                <a href="<?php echo e(route('patient.appointments.index')); ?>" class="text-sm px-3 py-2 border rounded-lg hover:bg-gray-50">Mis citas</a>
            </div>
        </div>
     <?php $__env->endSlot(); ?>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid md:grid-cols-3 gap-4">
                <a href="<?php echo e(route('patient.appointments.create')); ?>" class="bg-white rounded-xl border p-5 hover:border-teal-300 transition">
                    <div class="text-teal-600 font-semibold">Agendar cita</div>
                    <p class="text-sm text-gray-500 mt-1">Elige especialidad, profesional y horario</p>
                </a>
                <a href="<?php echo e(route('patient.appointments.index')); ?>" class="bg-white rounded-xl border p-5 hover:border-teal-300 transition">
                    <div class="text-teal-600 font-semibold">Mis citas</div>
                    <p class="text-sm text-gray-500 mt-1">Consulta, reprograma o cancela</p>
                </a>
                <a href="<?php echo e(route('patient.waitlist.create')); ?>" class="bg-white rounded-xl border p-5 hover:border-teal-300 transition">
                    <div class="text-teal-600 font-semibold">Lista de espera</div>
                    <p class="text-sm text-gray-500 mt-1">Si no hay horario, te avisamos</p>
                </a>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium">Próximas citas</h3>
                    <a href="<?php echo e(route('patient.appointments.index')); ?>" class="text-sm text-teal-600 hover:underline">Ver todas</a>
                </div>
                <?php $__empty_1 = true; $__currentLoopData = $upcoming; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cita): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <a href="<?php echo e(route('patient.appointments.show', $cita)); ?>" class="border-b py-3 flex justify-between items-center block hover:bg-gray-50">
                        <div>
                            <div class="font-medium"><?php echo e($cita->professional->name ?? 'Profesional'); ?></div>
                            <div class="text-sm text-gray-500">
                                <?php echo e($cita->date->format('d/m/Y')); ?> · <?php echo e(\Carbon\Carbon::parse($cita->time)->format('H:i')); ?> · <?php echo e($cita->modality->label()); ?>

                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full bg-<?php echo e($cita->status->color()); ?>-100 text-<?php echo e($cita->status->color()); ?>-800">
                            <?php echo e($cita->status->label()); ?>

                        </span>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-gray-500">
                        No tienes citas próximas.
                        <a href="<?php echo e(route('patient.appointments.create')); ?>" class="text-teal-600 hover:underline">Agendar una cita</a>
                    </p>
                <?php endif; ?>
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
<?php /**PATH D:\OCTAVO\Proyecto Mentea\Mentea\resources\views/patient/dashboard.blade.php ENDPATH**/ ?>