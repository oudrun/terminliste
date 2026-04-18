<?php
declare(strict_types=1);

return [
    '/' => [App\Http\Controllers\HomeController::class, 'index'],
    '/apply.php' => [App\Http\Controllers\RegistrationController::class, 'handle'],
    '/admin.php' => [App\Http\Controllers\AdminController::class, 'handle'],
];
