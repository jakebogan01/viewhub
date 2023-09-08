<?php

use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Shared\CommentController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

require __DIR__ . '/auth.php';

Route::get('/', function() {
    return to_route('dashboard.index');
});

Route::middleware(['auth', 'verified'])->group(function () {

    // dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard/tasks/{task:slug}', [DashboardController::class, 'show'])->name('dashboard.show');
    Route::get('/dashboard/task/create', [DashboardController::class, 'create'])->name('dashboard.create');
    Route::post('/dashboard/task/create', [DashboardController::class, 'store']);
    Route::get('/dashboard/task/{task:slug}/edit', [DashboardController::class, 'edit']);
    Route::patch('/dashboard/task/{task}', [DashboardController::class, 'update']);
    Route::delete('/dashboard/task/{task}', [DashboardController::class, 'destroy']);
    Route::post('/dashboard/tasks/{task}/like', [DashboardController::class, 'toggle']);
    Route::post('/dashboard/task/upload', [ImageController::class, 'upload']);
    Route::delete('/dashboard/task/revert/{folder}', [ImageController::class, 'revert']);
    Route::delete('/dashboard/task/delete/{image}', [ImageController::class, 'destroy']);

    // comments
    Route::post('/dashboard/comment/create', [CommentController::class, 'storeComment']);
    Route::post('/dashboard/comment/reply/create', [CommentController::class, 'storeReply']);
    Route::delete('/dashboard/comment/{comment}', [CommentController::class, 'destroyComment']);
    Route::delete('/dashboard/comment/reply/{reply}', [CommentController::class, 'destroyReply']);
    Route::patch('/dashboard/comment/{comment}', [CommentController::class, 'updateComment']);
    Route::patch('/dashboard/comment/reply/{reply}', [CommentController::class, 'updateReply']);

    // miscellaneous
    Route::post('/update-dark-mode', [DashboardController::class, 'enableDarkMode']);
    Route::post('/notification-mark-read', NotificationController::class);
});

Route::get('/about', function () {
    return Inertia::render('About');
})->middleware(['auth', 'verified'])->name('about');
