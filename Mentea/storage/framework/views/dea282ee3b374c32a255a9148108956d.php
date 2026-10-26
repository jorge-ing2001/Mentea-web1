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
            <h2 class="mentea-page-title">Roles y permisos (Spatie)</h2>
            <a href="<?php echo e(route('super.roles.users')); ?>" class="btn-mentea-outline text-sm">Asignar roles a usuarios</a>
        </div>
     <?php $__env->endSlot(); ?>
    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <?php if(session('success')): ?>
                <div class="alert-success"><?php echo e(session('success')); ?></div>
            <?php endif; ?>
            <p class="text-sm text-slate-600">
                Los roles se gestionan con <strong>Spatie Laravel Permission</strong>.
                Puedes ajustar los permisos de cada rol sin tocar código.
            </p>
            <div class="grid sm:grid-cols-2 gap-4">
                <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="mentea-card p-5">
                        <div class="flex justify-between items-start gap-2">
                            <div>
                                <h3 class="font-semibold text-slate-900"><?php echo e($role->name); ?></h3>
                                <p class="text-xs text-slate-500 mt-1"><?php echo e($role->users_count); ?> usuario(s)</p>
                            </div>
                            <a href="<?php echo e(route('super.roles.edit', $role)); ?>" class="text-sm text-teal-700 hover:underline">Editar permisos</a>
                        </div>
                        <div class="mt-3 flex flex-wrap gap-1">
                            <?php $__empty_1 = true; $__currentLoopData = $role->permissions->take(8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-teal-50 text-teal-800"><?php echo e($perm->name); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <span class="text-xs text-slate-400">Sin permisos</span>
                            <?php endif; ?>
                            <?php if($role->permissions->count() > 8): ?>
                                <span class="text-[10px] text-slate-400">+<?php echo e($role->permissions->count() - 8); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
<?php /**PATH D:\OCTAVO\Proyecto Mentea\Mentea\resources\views/super-admin/roles/index.blade.php ENDPATH**/ ?>