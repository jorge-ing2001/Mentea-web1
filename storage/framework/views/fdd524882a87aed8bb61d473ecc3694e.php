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
        <h2 class="mentea-page-title">Configuración del sistema</h2>
     <?php $__env->endSlot(); ?>
    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <?php if(session('success')): ?>
                <div class="alert-success mb-4"><?php echo e(session('success')); ?></div>
            <?php endif; ?>
            <form method="POST" action="<?php echo e(route('super.settings.update')); ?>" class="mentea-card p-6 space-y-6">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <?php $__currentLoopData = $defs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $meta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div>
                        <label class="mentea-label" for="<?php echo e($key); ?>"><?php echo e($meta['label']); ?></label>
                        <?php if($meta['type'] === 'boolean'): ?>
                            <select name="<?php echo e($key); ?>" id="<?php echo e($key); ?>" class="mentea-input">
                                <option value="1" <?php if((string)$values[$key] === '1'): echo 'selected'; endif; ?>>Sí</option>
                                <option value="0" <?php if((string)$values[$key] === '0'): echo 'selected'; endif; ?>>No</option>
                            </select>
                        <?php else: ?>
                            <input type="<?php echo e($meta['type'] === 'integer' ? 'number' : 'text'); ?>"
                                   name="<?php echo e($key); ?>" id="<?php echo e($key); ?>"
                                   value="<?php echo e(old($key, $values[$key])); ?>"
                                   class="mentea-input">
                        <?php endif; ?>
                        <p class="text-xs text-slate-500 mt-1"><?php echo e($meta['help']); ?></p>
                        <?php $__errorArgs = [$key];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-sm text-red-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <button type="submit" class="btn-mentea">Guardar cambios</button>
            </form>
            <form method="POST" action="<?php echo e(route('super.mail.test')); ?>" class="mt-4">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn-mentea-outline">Enviar correo de prueba SMTP</button>
                <?php $__errorArgs = ['mail'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-sm text-red-600 mt-2"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </form>
            <div class="hidden">
            <form>
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
<?php /**PATH D:\OCTAVO\Proyecto Mentea\Mentea\resources\views/super-admin/settings/index.blade.php ENDPATH**/ ?>