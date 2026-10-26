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
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Agendar con <?php echo e($professional->name); ?></h2>
     <?php $__env->endSlot(); ?>
    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border p-6 mb-6 text-sm text-gray-600">
                <p><strong><?php echo e($professional->specialty); ?></strong> · <?php echo e($professional->profession); ?></p>
                <p class="mt-1"><?php echo e($professional->description); ?></p>
            </div>

            <form method="POST" action="<?php echo e(route('patient.appointments.store')); ?>" enctype="multipart/form-data" id="book-form" aria-label="Formulario de agendamiento de cita" class="bg-white rounded-xl shadow-sm border p-6 space-y-6">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="booking_token" value="<?php echo e(\Illuminate\Support\Str::uuid()); ?>">
                <input type="hidden" name="professional_id" value="<?php echo e($professional->id); ?>">

                <?php if($errors->any()): ?>
                    <div class="p-4 bg-red-50 text-red-700 rounded-lg text-sm">
                        <ul class="list-disc list-inside"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
                    </div>
                <?php endif; ?>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha *</label>
                    <select name="date" id="date" required class="w-full border-gray-300 rounded-lg">
                        <option value="">Selecciona una fecha</option>
                        <?php $__currentLoopData = $availableDates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($d); ?>" <?php if(old('date')===$d): echo 'selected'; endif; ?>><?php echo e(\Carbon\Carbon::parse($d)->locale('es')->isoFormat('dddd D [de] MMMM')); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hora *</label>
                    <div id="slots-container" class="grid grid-cols-3 sm:grid-cols-4 gap-2 min-h-[40px]">
                        <p class="col-span-full text-sm text-gray-400">Selecciona una fecha</p>
                    </div>
                    <input type="hidden" name="time" id="time" value="<?php echo e(old('time')); ?>" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Modalidad *</label>
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Motivo (opcional)</label>
                    <textarea name="reason" rows="2" class="w-full border-gray-300 rounded-lg"><?php echo e(old('reason')); ?></textarea>
                </div>

                <div class="border-t pt-4 space-y-4">
                    <p class="text-sm font-medium text-gray-800">Documentos obligatorios (PDF o imagen)</p>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Orden *</label>
                        <input type="file" name="order_document" accept=".pdf,.jpg,.jpeg,.png,.webp,application/pdf,image/*" required
                               class="w-full text-sm border border-gray-300 rounded-lg p-2">
                        <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG o WEBP · máx. 10 MB</p>
                        <?php $__errorArgs = ['order_document'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Autorización *</label>
                        <input type="file" name="authorization_document" accept=".pdf,.jpg,.jpeg,.png,.webp,application/pdf,image/*" required
                               class="w-full text-sm border border-gray-300 rounded-lg p-2">
                        <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG o WEBP · máx. 10 MB</p>
                        <?php $__errorArgs = ['authorization_document'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div class="flex gap-3">
                    <a href="<?php echo e(route('patient.appointments.create')); ?>" class="px-4 py-2 border rounded-lg">Volver</a>
                    <button type="submit" id="submit-btn" class="flex-1 bg-teal-600 text-white py-2.5 rounded-lg hover:bg-teal-700 disabled:opacity-50" disabled>
                        Confirmar cita
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        const dateSelect = document.getElementById('date');
        const slotsContainer = document.getElementById('slots-container');
        const timeInput = document.getElementById('time');
        const submitBtn = document.getElementById('submit-btn');
        const slotsUrl = <?php echo json_encode(route('patient.appointments.slots', $professional), 512) ?>;
        function updateSubmit() {
            submitBtn.disabled = !dateSelect.value || !timeInput.value;
        }
        dateSelect.addEventListener('change', async () => {
            timeInput.value = ''; updateSubmit();
            slotsContainer.innerHTML = '<p class="col-span-full text-sm text-gray-400">Cargando...</p>';
            if (!dateSelect.value) return;
            try {
                const res = await fetch(`${slotsUrl}?date=${dateSelect.value}`, { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                const slots = data.slots || [];
                if (!slots.length) {
                    slotsContainer.innerHTML = '<p class="col-span-full text-sm text-amber-600">Sin horarios disponibles.</p>';
                    return;
                }
                slotsContainer.innerHTML = '';
                slots.forEach(slot => {
                    const btn = document.createElement('button');
                    btn.type = 'button'; btn.textContent = slot;
                    btn.className = 'px-3 py-2 rounded-lg border text-sm hover:border-teal-500 hover:bg-teal-50';
                    btn.addEventListener('click', () => {
                        document.querySelectorAll('#slots-container button').forEach(b => b.classList.remove('bg-teal-600','text-white'));
                        btn.classList.add('bg-teal-600','text-white');
                        timeInput.value = slot; updateSubmit();
                    });
                    slotsContainer.appendChild(btn);
                });
            } catch (e) {
                slotsContainer.innerHTML = '<p class="col-span-full text-sm text-red-600">Error al cargar horarios.</p>';
            }
        });
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
<?php /**PATH D:\OCTAVO\Proyecto Mentea\Mentea\resources\views/patient/appointments/schedule.blade.php ENDPATH**/ ?>