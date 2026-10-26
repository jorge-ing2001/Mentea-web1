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
     <?php $__env->slot('header', null, []); ?> <h2 class="mentea-page-title">Crear usuario</h2> <?php $__env->endSlot(); ?>
    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="<?php echo e(route('super.users.store')); ?>" class="mentea-card p-6 space-y-4" id="user-create-form">
                <?php echo csrf_field(); ?>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="mentea-label">Nombre completo *</label>
                        <input name="name" value="<?php echo e(old('name')); ?>" required class="mentea-input">
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label class="mentea-label">Tipo documento</label>
                        <select name="document_type" class="mentea-input">
                            <?php $__currentLoopData = ['CC','CE','TI','PA','RC','OTRO']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($t); ?>" <?php if(old('document_type','CC')===$t): echo 'selected'; endif; ?>><?php echo e($t); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label class="mentea-label">Número documento *</label>
                        <input name="document_number" value="<?php echo e(old('document_number')); ?>" required class="mentea-input">
                        <?php $__errorArgs = ['document_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label class="mentea-label">Correo *</label>
                        <input type="email" name="email" value="<?php echo e(old('email')); ?>" required class="mentea-input">
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label class="mentea-label">Teléfono</label>
                        <input name="phone" value="<?php echo e(old('phone')); ?>" class="mentea-input">
                    </div>
                    <div>
                        <label class="mentea-label">Contraseña *</label>
                        <input type="password" name="password" required class="mentea-input">
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label class="mentea-label">Confirmar contraseña *</label>
                        <input type="password" name="password_confirmation" required class="mentea-input">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mentea-label">Rol *</label>
                        <select name="role" id="role" class="mentea-input" required>
                            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($r->value); ?>" <?php if(old('role')===$r->value): echo 'selected'; endif; ?>><?php echo e($r->label()); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <div id="patient-fields" class="space-y-3 border-t pt-4">
                    <p class="text-sm font-semibold text-slate-700">Datos de paciente</p>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <div><label class="mentea-label">EPS</label><input name="eps" class="mentea-input" value="<?php echo e(old('eps')); ?>"></div>
                        <div><label class="mentea-label">Contacto emergencia</label><input name="emergency_contact_name" class="mentea-input" value="<?php echo e(old('emergency_contact_name')); ?>"></div>
                        <div><label class="mentea-label">Tel. emergencia</label><input name="emergency_contact_phone" class="mentea-input" value="<?php echo e(old('emergency_contact_phone')); ?>"></div>
                    </div>
                    <label class="flex gap-2 text-sm"><input type="checkbox" name="consent_informed" value="1" class="rounded text-teal-600"> Consentimiento informado</label>
                    <label class="flex gap-2 text-sm"><input type="checkbox" name="consent_data" value="1" class="rounded text-teal-600"> Tratamiento de datos (Ley 1581)</label>
                </div>

                <div id="pro-fields" class="space-y-3 border-t pt-4 hidden">
                    <p class="text-sm font-semibold text-slate-700">Datos de profesional</p>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <div><label class="mentea-label">Especialidad</label><input name="specialty" class="mentea-input" value="<?php echo e(old('specialty')); ?>"></div>
                        <div><label class="mentea-label">Profesión</label><input name="profession" class="mentea-input" value="<?php echo e(old('profession')); ?>"></div>
                        <div><label class="mentea-label">Duración cita (min)</label><input type="number" name="appointment_duration_minutes" class="mentea-input" value="<?php echo e(old('appointment_duration_minutes', 45)); ?>" min="15" max="180"></div>
                    </div>
                </div>

                <label class="flex gap-2 text-sm"><input type="checkbox" name="is_active" value="1" checked class="rounded text-teal-600"> Usuario activo</label>
                <button type="submit" class="btn-mentea w-full">Crear usuario</button>
            </form>
        </div>
    </div>
    <script>
        const role = document.getElementById('role');
        const patient = document.getElementById('patient-fields');
        const pro = document.getElementById('pro-fields');
        function toggle() {
            patient.classList.toggle('hidden', role.value !== 'patient');
            pro.classList.toggle('hidden', role.value !== 'professional');
        }
        role.addEventListener('change', toggle);
        toggle();
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
<?php /**PATH D:\OCTAVO\Proyecto Mentea\Mentea\resources\views/super-admin/users/create.blade.php ENDPATH**/ ?>