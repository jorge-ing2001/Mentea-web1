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
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Agendar cita — Mentea</h2>
     <?php $__env->endSlot(); ?>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <?php if(session('success')): ?>
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg"><?php echo e(session('success')); ?></div>
            <?php endif; ?>

            <!-- Filtro especialidades -->
            <div class="mb-6 flex flex-wrap gap-2">
                <a href="<?php echo e(route('patient.appointments.create')); ?>"
                   class="px-4 py-2 rounded-full text-sm <?php echo e(!$specialty ? 'bg-teal-600 text-white' : 'bg-white text-gray-700 border'); ?>">
                    Todas
                </a>
                <?php $__currentLoopData = $specialties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('patient.appointments.create', ['specialty' => $s])); ?>"
                       class="px-4 py-2 rounded-full text-sm <?php echo e($specialty === $s ? 'bg-teal-600 text-white' : 'bg-white text-gray-700 border'); ?>">
                        <?php echo e($s); ?>

                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php $__empty_1 = true; $__currentLoopData = $professionals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pro): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="bg-white rounded-xl shadow-sm border p-6 flex flex-col">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-14 h-14 rounded-full bg-teal-100 flex items-center justify-center text-teal-600 text-xl font-bold">
                                <?php echo e(strtoupper(substr($pro->name, 0, 1))); ?>

                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900"><?php echo e($pro->name); ?></h3>
                                <p class="text-sm text-teal-600"><?php echo e($pro->specialty); ?></p>
                            </div>
                        </div>
                        <?php if($pro->profession): ?>
                            <p class="text-sm text-gray-500 mb-2"><?php echo e($pro->profession); ?></p>
                        <?php endif; ?>
                        <?php if($pro->description): ?>
                            <p class="text-sm text-gray-600 mb-4 line-clamp-2"><?php echo e($pro->description); ?></p>
                        <?php endif; ?>
                        <div class="mt-auto">
                            <a href="<?php echo e(route('patient.appointments.schedule', $pro)); ?>"
                               class="block w-full text-center bg-teal-600 hover:bg-teal-700 text-white font-medium py-2.5 rounded-lg transition">
                                Elegir horario
                            </a>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="col-span-full bg-white rounded-xl p-8 text-center text-gray-500">
                        No hay profesionales disponibles en esta especialidad.
                    </div>
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
<?php /**PATH D:\OCTAVO\Proyecto Mentea\Mentea\resources\views/patient/appointments/create.blade.php ENDPATH**/ ?>