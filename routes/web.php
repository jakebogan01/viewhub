<?php

use App\Http\Controllers\Dashboard\DashboardController;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

require __DIR__ . '/auth.php';

Route::get('/', function() {
    return to_route('dashboard.index');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard/tasks/{task:slug}', [DashboardController::class, 'show'])->name('dashboard.show');
    Route::get('/dashboard/task/create', [DashboardController::class, 'create'])->name('dashboard.create');
    Route::post('/dashboard/task/create', [DashboardController::class, 'store']);
    Route::get('/dashboard/task/{task:slug}/edit', [DashboardController::class, 'edit']);
    Route::patch('/dashboard/task/{task}', [DashboardController::class, 'update']);
    Route::delete('/dashboard/task/{task}', [DashboardController::class, 'destroy']);
    Route::post('/dashboard/tasks/{task}/like', [DashboardController::class, 'toggle']);
});

Route::get('/about', function () {
    return Inertia::render('About');
})->middleware(['auth', 'verified'])->name('about');
