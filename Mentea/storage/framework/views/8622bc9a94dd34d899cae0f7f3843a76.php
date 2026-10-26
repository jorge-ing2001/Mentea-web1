<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="<?php echo e(auth()->user()?->preferred_theme === 'dark' ? 'dark' : ''); ?>" data-theme="<?php echo e(auth()->user()?->preferred_theme ?? 'system'); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
        <title><?php echo e(config('app.name', 'Mentea')); ?><?php if(isset($title)): ?> — <?php echo e($title); ?><?php endif; ?></title>
        <?php echo $__env->make('partials.brand-head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
        <link rel="stylesheet" href="<?php echo e(asset('css/mentea.css')); ?>">
    </head>
    <body class="font-sans antialiased">
    <a href="#main-content" class="mentea-skip-link">Saltar al contenido</a>
        <div class="mentea-page">
            <?php echo $__env->make('layouts.navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <?php if(isset($header)): ?>
                <header class="border-b border-slate-200/60 bg-white/70 backdrop-blur-sm">
                    <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                        <?php echo e($header); ?>

                    </div>
                </header>
            <?php endif; ?>

            <main id="main-content" class="pb-12">
                <?php echo e($slot); ?>

            </main>

            <footer class="border-t border-slate-200/80 py-6 text-center text-xs text-slate-400">
                <span class="mentea-brand-wordmark text-sm" style="font-size:0.85rem;letter-spacing:0.06em;">MENTEA</span>
                <span class="mx-2">·</span>
                Conecta con tu bienestar mental · © <?php echo e(date('Y')); ?>

            </footer>
        </div>
    <script>
(function(){
  var t = document.documentElement.getAttribute('data-theme');
  if (t === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches) {
    document.documentElement.classList.add('dark');
  }
})();
</script>
</body>
</html>
<?php /**PATH D:\OCTAVO\Proyecto Mentea\Mentea\resources\views/layouts/app.blade.php ENDPATH**/ ?>