<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mentea — Conecta con tu bienestar mental</title>
    <?php echo $__env->make('partials.brand-head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/mentea.css')); ?>">
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-800">
    <header class="mentea-header">
        <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2">
                <img src="<?php echo e(asset('images/brand/logo-symbol.svg')); ?>" alt="Mentea" class="h-10 w-10"
                     onerror="this.src='<?php echo e(asset('images/brand/logo-symbol.png')); ?>'">
                <div class="leading-tight">
                    <div class="mentea-brand-wordmark text-lg" style="font-size:1.15rem;">MENTEA</div>
                    <div class="text-[10px] text-mentea-600 hidden sm:block">Conecta con tu bienestar mental</div>
                </div>
            </a>
            <nav class="flex items-center gap-2 text-sm">
                <?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(route('dashboard')); ?>" class="btn-mentea">Mi panel</a>
                <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>" class="btn-mentea-ghost">Iniciar sesión</a>
                    <a href="<?php echo e(route('register')); ?>" class="btn-mentea">Registrarse</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main>
        <section class="relative overflow-hidden">
            <div class="absolute inset-0 bg-mentea-gradient"></div>
            <div class="relative max-w-6xl mx-auto px-4 py-16 md:py-24 text-white grid md:grid-cols-2 gap-10 items-center">
                <div>
                    <p class="text-mentea-mint text-sm font-semibold tracking-wide uppercase mb-3">Salud mental accesible</p>
                    <h1 class="font-display text-4xl md:text-5xl font-bold leading-tight text-balance">
                        Conecta con tu bienestar mental
                    </h1>
                    <p class="mt-5 text-lg text-white/90 max-w-xl leading-relaxed">
                        Agenda citas con psicólogos, psiquiatras y neuropsicólogos. Atención presencial o virtual, con recordatorios y acompañamiento.
                    </p>
                    <p class="mt-2 text-sm text-white/70">Conecta · Exprésate · Cuida tu mente</p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <?php if(auth()->guard()->check()): ?>
                            <a href="<?php echo e(route('dashboard')); ?>" class="px-6 py-3 rounded-xl bg-white text-mentea-800 font-semibold hover:bg-mentea-50 transition shadow-sm">Ir al panel</a>
                        <?php else: ?>
                            <a href="<?php echo e(route('register')); ?>" class="px-6 py-3 rounded-xl bg-white text-mentea-800 font-semibold hover:bg-mentea-50 transition shadow-sm">Crear cuenta gratis</a>
                            <a href="<?php echo e(route('login')); ?>" class="px-6 py-3 rounded-xl border border-white/40 text-white font-medium hover:bg-white/10 transition">Ya tengo cuenta</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="hidden md:flex justify-center">
                    <img src="<?php echo e(asset('images/brand/logo-principal-oscuro.png')); ?>"
                         alt="Mentea logo"
                         class="max-h-72 w-auto object-contain drop-shadow-xl rounded-2xl">
                </div>
            </div>
        </section>

        <section class="max-w-6xl mx-auto px-4 py-16">
            <h2 class="font-display text-2xl font-bold text-center text-mentea-900 mb-10">¿Cómo funciona?</h2>
            <div class="grid md:grid-cols-3 gap-6">
                <?php $__currentLoopData = [
                    ['1', 'Regístrate', 'Crea tu cuenta en minutos con tu documento e email.'],
                    ['2', 'Elige profesional y horario', 'Filtra por especialidad y reserva un espacio disponible en tiempo real.'],
                    ['3', 'Asiste y cuida tu bienestar', 'Recibe recordatorios y, si es virtual, el enlace de Meet automáticamente.'],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$n, $t, $d]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="mentea-card-hover p-6">
                    <div class="w-10 h-10 rounded-full bg-mentea-100 text-mentea-700 flex items-center justify-center font-bold mb-4"><?php echo e($n); ?></div>
                    <h3 class="font-semibold text-lg text-slate-900 mb-2"><?php echo e($t); ?></h3>
                    <p class="text-slate-600 text-sm leading-relaxed"><?php echo e($d); ?></p>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </section>

        <section class="bg-white border-y border-slate-100">
            <div class="max-w-6xl mx-auto px-4 py-16">
                <h2 class="font-display text-2xl font-bold text-center text-mentea-900 mb-10">Especialidades</h2>
                <div class="grid sm:grid-cols-3 gap-6">
                    <div class="rounded-2xl bg-gradient-to-br from-teal-50 to-emerald-50 p-6 border border-teal-100">
                        <h3 class="font-semibold text-lg text-teal-900">Psicología</h3>
                        <p class="mt-2 text-sm text-slate-600">Terapia y acompañamiento emocional.</p>
                    </div>
                    <div class="rounded-2xl bg-gradient-to-br from-cyan-50 to-teal-50 p-6 border border-cyan-100">
                        <h3 class="font-semibold text-lg text-teal-900">Psiquiatría</h3>
                        <p class="mt-2 text-sm text-slate-600">Evaluación y tratamiento médico especializado.</p>
                    </div>
                    <div class="rounded-2xl bg-gradient-to-br from-emerald-50 to-green-50 p-6 border border-emerald-100">
                        <h3 class="font-semibold text-lg text-teal-900">Neuropsicología</h3>
                        <p class="mt-2 text-sm text-slate-600">Evaluación y rehabilitación cognitiva.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="max-w-6xl mx-auto px-4 py-16 text-center">
            <img src="<?php echo e(asset('images/brand/favicon-heart.png')); ?>" alt="" class="h-12 w-12 mx-auto mb-4 object-contain opacity-90">
            <h2 class="font-display text-2xl font-bold text-mentea-900 mb-3">¿Listo para dar el primer paso?</h2>
            <p class="text-slate-600 mb-6">Únete a Mentea y agenda tu cita cuando te convenga.</p>
            <?php if(auth()->guard()->guest()): ?>
                <a href="<?php echo e(route('register')); ?>" class="btn-mentea px-8 py-3 text-base">Comenzar ahora</a>
            <?php else: ?>
                <a href="<?php echo e(route('dashboard')); ?>" class="btn-mentea px-8 py-3 text-base">Ir a mi panel</a>
            <?php endif; ?>
        </section>
    </main>

    <footer class="border-t border-slate-200 py-10 text-center text-sm text-slate-500">
        <div class="mentea-brand-wordmark text-mentea-800 mb-1" style="font-size:1rem;">MENTEA</div>
        <p class="text-mentea-600 text-xs mb-2">Conecta con tu bienestar mental.</p>
        <p class="text-xs text-slate-400">© <?php echo e(date('Y')); ?> Mentea — Plataforma de agendamiento de salud mental</p>
    </footer>
</body>
</html>
<?php /**PATH D:\OCTAVO\Proyecto Mentea\Mentea\resources\views/welcome.blade.php ENDPATH**/ ?>