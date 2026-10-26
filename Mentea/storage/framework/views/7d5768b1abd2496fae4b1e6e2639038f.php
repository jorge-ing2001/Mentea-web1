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
        <h2 class="mentea-page-title">Nuevo diligenciamiento / evento</h2>
     <?php $__env->endSlot(); ?>
    <div class="py-8">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="<?php echo e(route('super.tickets.store')); ?>" class="mentea-card p-6 space-y-4">
                <?php echo csrf_field(); ?>
                <p class="text-sm text-slate-600">Registro de evento sobre la plataforma (no es un ticket de paciente).</p>
                <div>
                    <label class="mentea-label">Categoría *</label>
                    <select name="category" required class="mentea-input">
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($val); ?>"><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="mentea-label">Fecha del evento</label>
                    <input type="datetime-local" name="event_date" class="mentea-input">
                </div>
                <div>
                    <label class="mentea-label">Asunto *</label>
                    <input type="text" name="subject" required class="mentea-input" maxlength="150">
                </div>
                <div>
                    <label class="mentea-label">Descripción *</label>
                    <textarea name="message" rows="5" required class="mentea-input"></textarea>
                </div>
                <div>
                    <label class="mentea-label">Prioridad</label>
                    <select name="priority" class="mentea-input">
                        <option value="low">Baja</option>
                        <option value="medium" selected>Media</option>
                        <option value="high">Alta</option>
                        <option value="urgent">Urgente</option>
                    </select>
                </div>
                <div>
                    <label class="mentea-label">Estado</label>
                    <select name="status" class="mentea-input">
                        <option value="resolved" selected>Resuelto / documentado</option>
                        <option value="open">Abierto</option>
                        <option value="in_progress">En progreso</option>
                        <option value="closed">Cerrado</option>
                    </select>
                </div>
                <button type="submit" class="btn-mentea w-full">Registrar</button>
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
<?php /**PATH D:\OCTAVO\Proyecto Mentea\Mentea\resources\views/super-admin/tickets/create.blade.php ENDPATH**/ ?>