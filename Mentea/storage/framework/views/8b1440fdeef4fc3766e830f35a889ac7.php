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
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Cita #<?php echo e($appointment->id); ?></h2>
     <?php $__env->endSlot(); ?>
    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <?php if(session('success')): ?>
                <div class="p-3 bg-green-100 text-green-800 rounded-lg text-sm"><?php echo e(session('success')); ?></div>
            <?php endif; ?>
            <div class="bg-white rounded-xl shadow-sm border p-6 space-y-3 text-sm">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-gray-500">Paciente</div>
                        <div class="font-medium text-base"><?php echo e($appointment->patient->name); ?></div>
                        <div class="text-xs text-gray-500"><?php echo e($appointment->patient->email); ?></div>
                        <?php if($appointment->patient->phone): ?>
                            <div class="text-xs text-gray-500"><?php echo e($appointment->patient->phone); ?></div>
                        <?php endif; ?>
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
                        <div class="text-gray-500">Estado</div>
                        <span class="px-2 py-0.5 text-xs rounded-full bg-<?php echo e($appointment->status->color()); ?>-100 text-<?php echo e($appointment->status->color()); ?>-800"><?php echo e($appointment->status->label()); ?></span>
                    </div>
                </div>
                <?php if($appointment->reason): ?>
                    <div><span class="text-gray-500">Motivo:</span> <?php echo e($appointment->reason); ?></div>
                <?php endif; ?>
                <?php if($appointment->meet_link): ?>
                    <div class="p-2 bg-blue-50 rounded">
                        <a href="<?php echo e($appointment->meet_link); ?>" target="_blank" class="text-teal-600 break-all"><?php echo e($appointment->meet_link); ?></a>
                    </div>
                <?php endif; ?>
            </div>

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

            <?php if(in_array($appointment->status->value, ['pending', 'confirmed'])): ?>
                <form method="POST" action="<?php echo e(route('professional.appointments.status', $appointment)); ?>" class="bg-white rounded-xl shadow-sm border p-6 space-y-4">
                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                    <h3 class="font-medium">Actualizar estado</h3>
                    <div>
                        <select name="status" required class="w-full border-gray-300 rounded-lg">
                            <option value="confirmed" <?php if($appointment->status->value==='confirmed'): echo 'selected'; endif; ?>>Confirmada</option>
                            <option value="completed">Completada</option>
                            <option value="no_show">No asistió</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Notas clínicas (internas)</label>
                        <textarea name="notes" rows="3" class="w-full border-gray-300 rounded-lg"><?php echo e(old('notes', $appointment->notes)); ?></textarea>
                    </div>
                    <div class="flex gap-3">
                        <a href="<?php echo e(route('professional.appointments.index')); ?>" class="px-4 py-2 border rounded-lg">Volver</a>
                        <button type="submit" class="flex-1 bg-teal-600 text-white py-2 rounded-lg hover:bg-teal-700">Guardar</button>
                    </div>
                </form>
            <?php else: ?>
                <a href="<?php echo e(route('professional.appointments.index')); ?>" class="inline-block px-4 py-2 border rounded-lg">Volver al listado</a>
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


<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewClinicalNotes', $appointment)): ?>
<div class="mentea-card p-5 mt-6">
    <h3 class="font-semibold text-slate-800 mb-2">Historia clínica (notas confidenciales)</h3>
    <p class="text-xs text-slate-500 mb-3">Visible solo para el profesional asignado y administradores.</p>
    <?php if($appointment->clinical_notes): ?>
        <div class="text-sm whitespace-pre-wrap bg-slate-50 rounded-lg p-3 mb-3"><?php echo e($appointment->clinical_notes); ?></div>
        <p class="text-xs text-slate-400">Actualizado <?php echo e($appointment->clinical_notes_updated_at?->format('d/m/Y H:i')); ?></p>
    <?php endif; ?>
    <form method="POST" action="<?php echo e(route('professional.clinical.store', $appointment)); ?>" class="space-y-3 mt-3">
        <?php echo csrf_field(); ?>

        <input type="hidden" name="status" value="<?php echo e($appointment->status->value); ?>">
        <label class="mentea-label" for="clinical_notes">Notas clínicas</label>
        <textarea id="clinical_notes" name="clinical_notes" rows="5" class="mentea-input" aria-label="Notas clínicas confidenciales"><?php echo e(old('clinical_notes', $appointment->clinical_notes)); ?></textarea>
        <button type="submit" class="btn-mentea text-sm">Guardar notas</button>
    </form>
</div>
<?php endif; ?>

<?php /**PATH D:\OCTAVO\Proyecto Mentea\Mentea\resources\views/professional/appointments/show.blade.php ENDPATH**/ ?>