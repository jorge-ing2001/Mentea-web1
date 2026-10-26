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
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Profesionales</h2>
            <a href="<?php echo e(route('admin.professionals.create')); ?>" class="bg-teal-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-teal-700">+ Nuevo</a>
        </div>
     <?php $__env->endSlot(); ?>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <?php if(session('success')): ?>
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-lg text-sm"><?php echo e(session('success')); ?></div>
            <?php endif; ?>
            <form method="GET" class="mb-4 flex flex-wrap gap-2">
                <input type="search" name="q" value="<?php echo e(request('q')); ?>" placeholder="Buscar..." class="border-gray-300 rounded-lg text-sm">
                <select name="specialty" class="border-gray-300 rounded-lg text-sm">
                    <option value="">Todas las especialidades</option>
                    <?php $__currentLoopData = $specialties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($s); ?>" <?php if(request('specialty')===$s): echo 'selected'; endif; ?>><?php echo e($s); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <select name="status" class="border-gray-300 rounded-lg text-sm">
                    <option value="">Todos los estados</option>
                    <option value="active" <?php if(request('status')==='active'): echo 'selected'; endif; ?>>Activos</option>
                    <option value="inactive" <?php if(request('status')==='inactive'): echo 'selected'; endif; ?>>Inactivos</option>
                </select>
                <button class="px-3 py-1.5 bg-gray-800 text-white text-sm rounded-lg">Filtrar</button>
            </form>
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <table class="min-w-full divide-y text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left">Nombre</th>
                            <th class="px-4 py-3 text-left">Especialidad</th>
                            <th class="px-4 py-3 text-left">Estado</th>
                            <th class="px-4 py-3 text-left">Citas</th>
                            <th class="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php $__empty_1 = true; $__currentLoopData = $professionals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pro): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="<?php echo e($pro->is_available ? '' : 'bg-gray-50 opacity-80'); ?>">
                                <td class="px-4 py-3">
                                    <div class="font-medium"><?php echo e($pro->name); ?></div>
                                    <div class="text-xs text-gray-500"><?php echo e($pro->email); ?></div>
                                </td>
                                <td class="px-4 py-3"><?php echo e($pro->specialty); ?></td>
                                <td class="px-4 py-3">
                                    <?php if($pro->is_available): ?>
                                        <span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-800 font-medium">Activo</span>
                                    <?php else: ?>
                                        <span class="px-2 py-0.5 text-xs rounded-full bg-gray-200 text-gray-700 font-medium">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3"><?php echo e($pro->appointments_count); ?></td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    <a href="<?php echo e(route('admin.professionals.edit', $pro)); ?>" class="text-teal-600 hover:underline">Editar</a>
                                    <a href="<?php echo e(route('admin.schedules.edit', $pro)); ?>" class="text-teal-600 hover:underline">Horarios</a>
                                    <form method="POST" action="<?php echo e(route('admin.professionals.toggle', $pro)); ?>" class="inline"
                                          onsubmit="return confirm('¿<?php echo e($pro->is_available ? 'Inactivar' : 'Activar'); ?> a <?php echo e($pro->name); ?>?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <button type="submit" class="text-sm <?php echo e($pro->is_available ? 'text-amber-600' : 'text-green-600'); ?> hover:underline">
                                            <?php echo e($pro->is_available ? 'Inactivar' : 'Activar'); ?>

                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Sin profesionales.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-4"><?php echo e($professionals->links()); ?></div>
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
<?php /**PATH D:\OCTAVO\Proyecto Mentea\Mentea\resources\views/admin/professionals/index.blade.php ENDPATH**/ ?>