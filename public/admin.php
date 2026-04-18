<?php
declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap/app.php';

use App\Http\Controllers\AdminController;

(new AdminController())->handle();
