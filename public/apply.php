<?php
declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap/app.php';

use App\Http\Controllers\RegistrationController;

$classId = (int) ($_GET['class_id'] ?? 0);
(new RegistrationController())->handle($classId);
