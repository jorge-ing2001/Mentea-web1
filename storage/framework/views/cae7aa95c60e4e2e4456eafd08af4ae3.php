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
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Lista de espera</h2>
            <a href="<?php echo e(route('patient.waitlist.create')); ?>" class="bg-teal-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-teal-700">+ Nueva solicitud</a>
        </div>
     <?php $__env->endSlot(); ?>
    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <?php if(session('success')): ?>
                <div class="p-3 bg-green-100 text-green-800 rounded-lg text-sm"><?php echo e(session('success')); ?></div>
            <?php endif; ?>
            <?php if($errors->any()): ?>
                <div class="p-3 bg-red-50 text-red-700 rounded-lg text-sm"><?php echo e($errors->first()); ?></div>
            <?php endif; ?>

            <div>
                <h3 class="font-medium text-gray-800 mb-3">Solicitudes activas</h3>
                <?php $__empty_1 = true; $__currentLoopData = $active; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <a href="<?php echo e(route('patient.waitlist.show', $e)); ?>" class="block bg-white rounded-xl border shadow-sm p-4 mb-3 hover:border-teal-300 transition">
                        <div class="flex flex-wrap justify-between gap-2 items-start">
                            <div>
                                <div class="font-medium"><?php echo e($e->specialty); ?></div>
                                <div class="text-sm text-gray-500 mt-1"><?php echo e($e->preferenceSummary()); ?></div>
                                <div class="text-xs text-gray-400 mt-1">Solicitada <?php echo e($e->created_at->format('d/m/Y H:i')); ?></div>
                            </div>
                            <span class="px-2.5 py-1 text-xs rounded-full font-medium bg-<?php echo e($e->status->color()); ?>-100 text-<?php echo e($e->status->color()); ?>-800">
                                <?php echo e($e->status->emoji()); ?> <?php echo e($e->status->label()); ?>

                            </span>
                        </div>
                        <?php if($e->isOfferActive()): ?>
                            <div class="mt-3 p-2 bg-blue-50 rounded-lg text-sm text-blue-800">
                                Oferta disponible · expira en <span class="font-mono font-semibold countdown" data-expires="<?php echo e($e->offer_expires_at->toIso8601String()); ?>">--:--</span>
                            </div>
                        <?php endif; ?>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="bg-white rounded-xl border p-6 text-center text-gray-500 text-sm">
                        No tienes solicitudes activas.
                        <a href="<?php echo e(route('patient.waitlist.create')); ?>" class="text-teal-600 hover:underline">Crear una</a>
                    </div>
                <?php endif; ?>
            </div>

            <div>
                <h3 class="font-medium text-gray-800 mb-3">Historial</h3>
                <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
                    <table class="min-w-full divide-y text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left">Especialidad</th>
                                <th class="px-4 py-3 text-left">Preferencia</th>
                                <th class="px-4 py-3 text-left">Estado</th>
                                <th class="px-4 py-3 text-left">Fecha solicitud</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <?php $__empty_1 = true; $__currentLoopData = $history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <a href="<?php echo e(route('patient.waitlist.show', $e)); ?>" class="text-teal-600 hover:underline"><?php echo e($e->specialty); ?></a>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600"><?php echo e($e->preferenceSummary()); ?></td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-0.5 text-xs rounded-full bg-<?php echo e($e->status->color()); ?>-100 text-<?php echo e($e->status->color()); ?>-800">
                                            <?php echo e($e->status->emoji()); ?> <?php echo e($e->status->label()); ?>

                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500"><?php echo e($e->created_at->format('d/m/Y')); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">Sin historial.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-3"><?php echo e($history->links()); ?></div>
            </div>
        </div>
    </div>
    <script>
        function tick() {
            document.querySelectorAll('.countdown').forEach(el => {
                const exp = new Date(el.dataset.expires).getTime();
                const diff = Math.max(0, Math.floor((exp - Date.now()) / 1000));
                const m = String(Math.floor(diff / 60)).padStart(2, '0');
                const s = String(diff % 60).padStart(2, '0');
                el.textContent = m + ':' + s;
            });
        }
        tick(); setInterval(tick, 1000);
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
<?php /**PATH D:\OCTAVO\Proyecto Mentea\Mentea\resources\views/patient/waitlist/index.blade.php ENDPATH**/ ?>