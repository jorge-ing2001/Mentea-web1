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
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detalle de cita</h2>
     <?php $__env->endSlot(); ?>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <?php if(session('success')): ?>
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg"><?php echo e(session('success')); ?></div>
            <?php endif; ?>

            <div class="bg-white rounded-xl shadow-sm border p-6 space-y-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-lg font-semibold"><?php echo e($appointment->professional->name); ?></h3>
                        <p class="text-sm text-teal-600"><?php echo e($appointment->professional->specialty); ?></p>
                    </div>
                    <span class="px-3 py-1 text-sm rounded-full bg-<?php echo e($appointment->status->color()); ?>-100 text-<?php echo e($appointment->status->color()); ?>-800">
                        <?php echo e($appointment->status->label()); ?>

                    </span>
                </div>

                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">Fecha</dt>
                        <dd class="font-medium"><?php echo e($appointment->date->format('d/m/Y')); ?></dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Hora</dt>
                        <dd class="font-medium"><?php echo e(\Carbon\Carbon::parse($appointment->time)->format('H:i')); ?></dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Modalidad</dt>
                        <dd class="font-medium"><?php echo e($appointment->modality->label()); ?></dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Duración</dt>
                        <dd class="font-medium"><?php echo e($appointment->professional->appointment_duration_minutes); ?> min</dd>
                    </div>
                </dl>

                <?php if($appointment->reason): ?>
                    <div>
                        <dt class="text-sm text-gray-500">Motivo</dt>
                        <dd class="text-sm"><?php echo e($appointment->reason); ?></dd>
                    </div>
                <?php endif; ?>

                <?php if($appointment->meet_link): ?>
                    <div class="p-3 bg-blue-50 rounded-lg">
                        <p class="text-sm text-blue-800 font-medium">Enlace de videollamada</p>
                        <a href="<?php echo e($appointment->meet_link); ?>" target="_blank" class="text-teal-600 text-sm break-all hover:underline">
                            <?php echo e($appointment->meet_link); ?>

                        </a>
                    </div>
                <?php endif; ?>

                <div class="flex gap-3 pt-4 border-t">
                    <a href="<?php echo e(route('patient.appointments.index')); ?>"
                       class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">Volver al listado</a>

                    
                <?php if($appointment->status->value === 'completed' && !$appointment->survey): ?>
                    <a href="<?php echo e(route('patient.surveys.create', $appointment)); ?>"
                       class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg">
                        Valorar esta cita
                    </a>
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

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('reschedule', $appointment)): ?>
                        <a href="<?php echo e(route('patient.appointments.edit', $appointment)); ?>"
                           class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg">
                            Reprogramar
                        </a>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('cancel', $appointment)): ?>
                        <form method="POST" action="<?php echo e(route('patient.appointments.cancel', $appointment)); ?>"
                              onsubmit="return confirm('¿Seguro que deseas cancelar esta cita?')">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">
                                Cancelar cita
                            </button>
                        </form>
                    <?php endif; ?>
                </div>

                <?php if(!$appointment->canBeCancelledByPatient() && $appointment->status->canBeCancelled()): ?>
                    <p class="text-xs text-amber-600">
                        Solo puedes cancelar con al menos 5 días de anticipación.
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

<?php if($appointment->modality?->value === 'virtual'): ?>
    <div class="mt-3 text-sm text-slate-600 mentea-card p-3">
        <strong>Videollamada:</strong>
        <?php if($appointment->meet_link): ?>
            <a href="<?php echo e($appointment->meet_link); ?>" target="_blank" rel="noopener" class="text-teal-700 underline"><?php echo e($appointment->meet_link); ?></a>
        <?php else: ?>
            El enlace se compartirá antes de la cita (correo o panel). No se generan salas ficticias en producción.
        <?php endif; ?>
    </div>
<?php endif; ?>

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

<?php if(in_array($appointment->status->value, ['pending','confirmed'], true) && !$appointment->patient_confirmed_at): ?>
<form method="POST" action="<?php echo e(route('patient.appointments.confirm', $appointment)); ?>" class="mt-4">
    <?php echo csrf_field(); ?>
    <button type="submit" class="btn-mentea">Confirmar asistencia</button>
</form>
<?php elseif($appointment->patient_confirmed_at): ?>
<p class="text-sm text-teal-700 mt-2">Confirmaste asistencia el <?php echo e($appointment->patient_confirmed_at->format('d/m/Y H:i')); ?>.</p>
<?php endif; ?>


<?php /**PATH D:\OCTAVO\Proyecto Mentea\Mentea\resources\views/patient/appointments/show.blade.php ENDPATH**/ ?>