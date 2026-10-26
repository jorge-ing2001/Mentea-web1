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
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nueva solicitud — Lista de espera</h2>
            <a href="<?php echo e(route('patient.waitlist.index')); ?>" class="text-sm text-teal-600 hover:underline">Mis solicitudes</a>
        </div>
     <?php $__env->endSlot(); ?>
    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="<?php echo e(route('patient.waitlist.store')); ?>" class="bg-white rounded-xl shadow-sm border p-6 space-y-5" id="waitlist-form">
                <?php echo csrf_field(); ?>
                <p class="text-sm text-gray-600">Indica tus preferencias. Te avisaremos cuando haya un horario compatible (prioridad por orden de llegada).</p>

                <?php if($errors->any()): ?>
                    <div class="p-3 bg-red-50 text-red-700 rounded-lg text-sm">
                        <ul class="list-disc list-inside"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
                    </div>
                <?php endif; ?>

                <div>
                    <label class="block text-sm font-medium mb-1">Especialidad *</label>
                    <select name="specialty" id="specialty" required class="w-full border-gray-300 rounded-lg">
                        <option value="">Selecciona</option>
                        <?php $__currentLoopData = $specialties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($s); ?>" <?php if(old('specialty')===$s): echo 'selected'; endif; ?>><?php echo e($s); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Profesional</label>
                    <select name="professional_id" id="professional_id" class="w-full border-gray-300 rounded-lg">
                        <option value="">Cualquier profesional</option>
                        <?php $__currentLoopData = $professionals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $specialty => $pros): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $__currentLoopData = $pros; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pro): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($pro->id); ?>" data-specialty="<?php echo e($pro->specialty); ?>" <?php if(old('professional_id')==$pro->id): echo 'selected'; endif; ?>>
                                    <?php echo e($pro->name); ?> — <?php echo e($pro->specialty); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Si eliges uno específico, solo se mostrarán los de la especialidad seleccionada.</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Fecha inicial *</label>
                        <input type="date" name="date_from" value="<?php echo e(old('date_from')); ?>" min="<?php echo e(date('Y-m-d')); ?>" required class="w-full border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Fecha final *</label>
                        <input type="date" name="date_to" value="<?php echo e(old('date_to')); ?>" min="<?php echo e(date('Y-m-d')); ?>" required class="w-full border-gray-300 rounded-lg">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Hora desde</label>
                        <input type="time" name="time_from" value="<?php echo e(old('time_from', '08:00')); ?>" class="w-full border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Hora hasta</label>
                        <input type="time" name="time_to" value="<?php echo e(old('time_to', '12:00')); ?>" class="w-full border-gray-300 rounded-lg">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Modalidad *</label>
                    <div class="flex gap-4">
                        <?php $__currentLoopData = $modalities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mod): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="flex items-center gap-2">
                                <input type="radio" name="modality" value="<?php echo e($mod->value); ?>" required
                                    <?php if(old('modality', 'presencial') === $mod->value): echo 'checked'; endif; ?> class="text-teal-600">
                                <span><?php echo e($mod->label()); ?></span>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Notas (opcional)</label>
                    <textarea name="notes" rows="2" class="w-full border-gray-300 rounded-lg"><?php echo e(old('notes')); ?></textarea>
                </div>

                <button type="submit" class="w-full bg-teal-600 text-white py-2.5 rounded-lg hover:bg-teal-700 font-medium">
                    Confirmar lista de espera
                </button>
            </form>
        </div>
    </div>
    <script>
        const specialty = document.getElementById('specialty');
        const proSelect = document.getElementById('professional_id');
        function filterPros() {
            const s = specialty.value;
            Array.from(proSelect.options).forEach((opt, i) => {
                if (i === 0) return; // Cualquiera
                const match = !s || opt.dataset.specialty === s;
                opt.hidden = !match;
                if (!match && opt.selected) proSelect.value = '';
            });
        }
        specialty.addEventListener('change', filterPros);
        filterPros();
    </script>
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
<?php /**PATH D:\OCTAVO\Proyecto Mentea\Mentea\resources\views/patient/waitlist/create.blade.php ENDPATH**/ ?>