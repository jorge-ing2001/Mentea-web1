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
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Notificaciones</h2>
            <?php if(Auth::user()->unreadNotifications->isNotEmpty()): ?>
                <form method="POST" action="<?php echo e(route('notifications.read-all')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="text-sm text-teal-600 hover:underline">Marcar todas como leídas</button>
                </form>
            <?php endif; ?>
        </div>
     <?php $__env->endSlot(); ?>
    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <?php if(session('success')): ?>
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-lg text-sm"><?php echo e(session('success')); ?></div>
            <?php endif; ?>
            <div class="bg-white rounded-xl shadow-sm border divide-y">
                <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php $data = $n->data; ?>
                    <a href="<?php echo e(route('notifications.read', $n->id)); ?>"
                       class="block px-5 py-4 hover:bg-gray-50 transition <?php echo e($n->read_at ? 'opacity-70' : 'bg-teal-50/40'); ?>">
                        <div class="flex justify-between gap-3">
                            <div>
                                <div class="font-medium text-sm text-gray-900"><?php echo e($data['title'] ?? 'Aviso'); ?></div>
                                <div class="text-sm text-gray-600 mt-0.5"><?php echo e($data['body'] ?? ''); ?></div>
                                <div class="text-xs text-gray-400 mt-1"><?php echo e($n->created_at->diffForHumans()); ?></div>
                            </div>
                            <?php if (! ($n->read_at)): ?>
                                <span class="shrink-0 w-2 h-2 mt-2 rounded-full bg-teal-500"></span>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="px-5 py-10 text-center text-gray-500 text-sm">No tienes notificaciones.</div>
                <?php endif; ?>
            </div>
            <div class="mt-4"><?php echo e($notifications->links()); ?></div>
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
<?php /**PATH D:\OCTAVO\Proyecto Mentea\Mentea\resources\views/notifications/index.blade.php ENDPATH**/ ?>