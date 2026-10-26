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
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestionar cita #<?php echo e($appointment->id); ?></h2>
     <?php $__env->endSlot(); ?>
    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <?php if(session('success')): ?>
                <div class="p-3 bg-green-100 text-green-800 rounded-lg text-sm"><?php echo e(session('success')); ?></div>
            <?php endif; ?>
            <div class="bg-white rounded-xl shadow-sm border p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <div class="text-gray-500">Paciente</div>
                        <div class="font-medium"><?php echo e($appointment->patient->name); ?></div>
                        <div class="text-xs text-gray-500"><?php echo e($appointment->patient->email); ?> · <?php echo e($appointment->patient->document_number); ?></div>
                    </div>
                    <div>
                        <div class="text-gray-500">Profesional</div>
                        <div class="font-medium"><?php echo e($appointment->professional->name); ?></div>
                        <div class="text-xs text-gray-500"><?php echo e($appointment->professional->specialty); ?></div>
                    </div>
                    <div>
                        <div class="text-gray-500">Fecha y hora</div>
                        <div class="font-medium"><?php echo e($appointment->date->format('d/m/Y')); ?> · <?php echo e(\Carbon\Carbon::parse($appointment->time)->format('H:i')); ?></div>
                    </div>
                    <div>
                        <div class="text-gray-500">Modalidad</div>
                        <div class="font-medium"><?php echo e($appointment->modality->label()); ?></div>
                    </div>
                    <div>
                        <div class="text-gray-500">Estado actual</div>
                        <span class="px-2 py-0.5 text-xs rounded-full bg-<?php echo e($appointment->status->color()); ?>-100 text-<?php echo e($appointment->status->color()); ?>-800">
                            <?php echo e($appointment->status->label()); ?>

                        </span>
                    </div>
                    <?php if($appointment->meet_link): ?>
                        <div>
                            <div class="text-gray-500">Meet</div>
                            <a href="<?php echo e($appointment->meet_link); ?>" target="_blank" class="text-teal-600 text-sm break-all"><?php echo e($appointment->meet_link); ?></a>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if($appointment->reason): ?>
                    <div class="text-sm"><span class="text-gray-500">Motivo:</span> <?php echo e($appointment->reason); ?></div>
                <?php endif; ?>
                <?php if (isset($component)) { $__componentOriginald2f916138d7c69bed97317966aa36317 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald2f916138d7c69bed97317966aa36317 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.appointment-documents','data' => ['appointment' => $appointment]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('appointment-documents'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['appointment' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($appointment)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald2f916138d7c69bed97317966aa36317)): ?>
<?php $attributes = $__attributesOriginald2f916138d7c69bed97317966aa36317; ?>
<?php unset($__attributesOriginald2f916138d7c69bed97317966aa36317); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald2f916138d7c69bed97317966aa36317)): ?>
<?php $component = $__componentOriginald2f916138d7c69bed97317966aa36317; ?>
<?php unset($__componentOriginald2f916138d7c69bed97317966aa36317); ?>
<?php endif; ?>

                <?php if($appointment->notes): ?>
                    <div class="text-sm"><span class="text-gray-500">Notas:</span> <?php echo e($appointment->notes); ?></div>
                <?php endif; ?>
            </div>

            <form method="POST" action="<?php echo e(route('admin.appointments.status', $appointment)); ?>" class="bg-white rounded-xl shadow-sm border p-6 space-y-4">
                <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                <h3 class="font-medium">Cambiar estado</h3>
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Nuevo estado</label>
                    <select name="status" required class="w-full border-gray-300 rounded-lg">
                        <?php $__currentLoopData = \App\Enums\AppointmentStatus::cases(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($st->value); ?>" <?php if($appointment->status === $st): echo 'selected'; endif; ?>><?php echo e($st->label()); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Notas internas</label>
                    <textarea name="notes" rows="2" class="w-full border-gray-300 rounded-lg"><?php echo e(old('notes', $appointment->notes)); ?></textarea>
                </div>
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Motivo de cancelación (si aplica)</label>
                    <input type="text" name="cancellation_reason" value="<?php echo e(old('cancellation_reason')); ?>" class="w-full border-gray-300 rounded-lg">
                </div>
                <div class="flex gap-3">
                    <a href="<?php echo e(route('admin.appointments.index')); ?>" class="px-4 py-2 border rounded-lg">Volver</a>
                    <button type="submit" class="flex-1 bg-teal-600 text-white py-2 rounded-lg hover:bg-teal-700">Actualizar</button>
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
<?php /**PATH D:\OCTAVO\Proyecto Mentea\Mentea\resources\views/admin/appointments/show.blade.php ENDPATH**/ ?>