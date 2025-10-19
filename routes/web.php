<?php

use App\Http\Controllers\AccountController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified', RoleMiddleware::class])->name('dashboard');

Route::get('/account/{account}/summary', [AccountController::class, 'summary'])
    ->middleware(['auth'])
    ->name('account.summary');

Route::get('/account/{account}/history', [AccountController::class, 'history'])
    ->middleware(['auth'])
    ->name('account.summary');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
