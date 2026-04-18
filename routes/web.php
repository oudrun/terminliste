<?php

declare(strict_types=1);

use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::match(['get', 'post'], '/apply', [RegistrationController::class, 'handle'])->name('apply');
Route::match(['get', 'post'], '/admin', [AdminController::class, 'handle'])->name('admin');
