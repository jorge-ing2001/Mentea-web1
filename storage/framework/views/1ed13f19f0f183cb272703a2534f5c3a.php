<?php $p = $professional ?? null; ?>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
    <input type="text" name="name" value="<?php echo e(old('name', $p->name ?? '')); ?>" required class="w-full border-gray-300 rounded-lg">
    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>
<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Especialidad *</label>
        <input type="text" name="specialty" value="<?php echo e(old('specialty', $p->specialty ?? '')); ?>" required list="specialties" class="w-full border-gray-300 rounded-lg">
        <datalist id="specialties">
            <option value="Psicología"><option value="Psiquiatría"><option value="Neuropsicología">
        </datalist>
        <?php $__errorArgs = ['specialty'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Profesión</label>
        <input type="text" name="profession" value="<?php echo e(old('profession', $p->profession ?? '')); ?>" class="w-full border-gray-300 rounded-lg">
    </div>
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
    <input type="email" name="email" value="<?php echo e(old('email', $p->email ?? '')); ?>" required class="w-full border-gray-300 rounded-lg">
    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>
<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
        <input type="text" name="phone" value="<?php echo e(old('phone', $p->phone ?? '')); ?>" class="w-full border-gray-300 rounded-lg">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Duración cita (min) *</label>
        <input type="number" name="appointment_duration_minutes" value="<?php echo e(old('appointment_duration_minutes', $p->appointment_duration_minutes ?? 45)); ?>" min="15" max="180" required class="w-full border-gray-300 rounded-lg">
    </div>
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
    <textarea name="description" rows="3" class="w-full border-gray-300 rounded-lg"><?php echo e(old('description', $p->description ?? '')); ?></textarea>
</div>
<div class="flex items-center gap-2">
    <input type="checkbox" name="is_available" value="1" id="is_available"
           <?php if(old('is_available', $p->is_available ?? true)): echo 'checked'; endif; ?> class="rounded text-teal-600">
    <label for="is_available" class="text-sm">Disponible para agendar</label>
</div>
<?php /**PATH D:\OCTAVO\Proyecto Mentea\Mentea\resources\views/admin/professionals/_form.blade.php ENDPATH**/ ?>