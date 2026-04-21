<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($pageTitle ?? 'Terminliste for hundeprøver'); ?></title>
    <link rel="stylesheet" href="<?php echo e(asset('assets/styles.css')); ?>">
</head>
<body>
<header>
    <div class="container">
        <h1>Terminliste for hundeprøver</h1>
        <p>Laravel og MariaDB for administrasjon av hundeprøver.</p>
        <nav>
            <a href="<?php echo e(route('home')); ?>">Terminliste</a>
            <a href="<?php echo e(route('admin')); ?>">Administrasjon</a>
        </nav>
    </div>
</header>

<main class="container">
    <?php if(session('flash')): ?>
        <div class="notice <?php echo e(session('flash.type')); ?>"><?php echo e(session('flash.message')); ?></div>
    <?php endif; ?>

    <?php echo $__env->yieldContent('content'); ?>
</main>
</body>
</html>
<?php /**PATH /var/www/html/resources/views/layouts/app.blade.php ENDPATH**/ ?>