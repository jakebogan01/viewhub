<?php

use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\SettingsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Shared\CommentController;
use App\Http\Controllers\Shared\ImageController;
use App\Models\Image;
use App\Models\Project;
use App\Models\Tag;
use App\Models\Task;
use App\Models\TemporaryImage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Illuminate\Http\Request;


require __DIR__ . '/auth.php';

Route::get('/', function() {
    return Inertia::render('Index');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::group([
        'prefix' => 'admin',
        'middleware' => 'is_admin',
        'as' => 'admin.',
        ], function () {
            Route::get('/test', function() {
                return Inertia::render('Test');
            });
    });

    Route::group([
//        'prefix' => 'client',
//        'as' => 'client.',
    ], function () {
        // client intro
        Route::get('/project/create', function () {

            if (auth()->user()->onboarded) {
                return to_route('dashboard.projects');
            }

            return Inertia::render('Auth/ClientOnboarding', [
                'tags' => Tag::all(),
            ]);
        })->name('Auth.ClientOnboarding');

        Route::post('/project/create', function (Request $request) {

            $request->validate([
                'project_name' => 'required|string|max:255',
            ]);

            $attributes = request()->validate([
                'title' => 'required',
                'description' => 'required',
                'priority' => 'required|integer|between:0,1',
                'tag_id' => ['required', Rule::exists('tags', 'id')],
            ]);

            $project = Project::create([
                'company_id' => 1,
                'name' => $request->project_name,
            ]);

            if ($request->due_date !== null) {
                $attributes['due_date'] = request()->validate([
                    'due_date' => 'date|after_or_equal:today',
                ])['due_date'];
            }
            $attributes['project_id'] = $project->id;
            $attributes['user_id'] = auth()->id();
            $attributes['status_id'] = 1;
            $attributes['slug'] = Str::slug($attributes['title']) . '-' . rand(100, 999);

            $task = Task::create($attributes);

            $temporaryImages = TemporaryImage::whereIn('folder', $request->images)->get();
            foreach($temporaryImages as $temporaryImage) {
                File::copyDirectory(public_path() . '/tmpimages', public_path() . '/images');
                Image::create([
                    'task_id' => $task->id,
                    'name' => $temporaryImage->file,
                    'path' => $temporaryImage->folder . '/' . $temporaryImage->file,
                ]);
                File::cleanDirectory(public_path() . '/tmpimages/user' . auth()->user()->id);
                $temporaryImage->delete();
            }

            auth()->user()->update([
                'onboarded' => true,
            ]);

            return to_route('dashboard.projects')->with('message', 'Project created successfully!');
        });

        // dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
        Route::get('/dashboard/tasks/{task:slug}', [DashboardController::class, 'show'])->name('dashboard.show');
        Route::get('/dashboard/task/create', [DashboardController::class, 'create'])->name('dashboard.create');
        Route::post('/dashboard/task/create', [DashboardController::class, 'storeTask']);
        Route::get('/dashboard/task/{task:slug}/edit', [DashboardController::class, 'edit']);
        Route::patch('/dashboard/task/{task}', [DashboardController::class, 'update']);
        Route::delete('/dashboard/task/{task}', [DashboardController::class, 'destroyTask']);
        Route::get('/dashboard/projects', [DashboardController::class, 'allProjects'])->name('dashboard.projects');
        Route::delete('/dashboard/project/{project}', [DashboardController::class, 'destroyProject']);
        Route::post('/dashboard/project/create', [DashboardController::class, 'storeProject']);

        // images
        Route::post('/dashboard/image/upload', [ImageController::class, 'upload']);
        Route::delete('/dashboard/image/revert/{folder}', [ImageController::class, 'revert']);
        Route::delete('/dashboard/image/delete/{image}', [ImageController::class, 'destroy']);

        // settings
        Route::get('/dashboard/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::patch('/dashboard/settings/update/information', [SettingsController::class, 'updateinformation']);
        Route::patch('/dashboard/settings/update/password', [SettingsController::class, 'updatePassword']);
        Route::delete('/dashboard/settings/delete/avatar', [SettingsController::class, 'deleteAvatar']);
        Route::delete('/dashboard/settings/delete/account', [SettingsController::class, 'deleteAccount']);
        Route::post('/dashboard/settings/create/review', [SettingsController::class, 'store']);

        // comments
        Route::post('/dashboard/comment/create', [CommentController::class, 'storeComment']);
        Route::post('/dashboard/comment/reply/create', [CommentController::class, 'storeReply']);
        Route::post('/dashboard/comment/comment-reply/create', [CommentController::class, 'storeCommentReply']);
        Route::delete('/dashboard/comment/{comment}', [CommentController::class, 'destroyComment']);
        Route::delete('/dashboard/comment/reply/{reply}', [CommentController::class, 'destroyReply']);
        Route::patch('/dashboard/comment/{comment}', [CommentController::class, 'updateComment']);
        Route::patch('/dashboard/comment/reply/{reply}', [CommentController::class, 'updateReply']);

        // miscellaneous
        Route::post('/update-dark-mode', [DashboardController::class, 'enableDarkMode']);
        Route::post('/notification-mark-read', [NotificationController::class, 'markRead']);
        Route::post('/notification/{task}/like', [NotificationController::class, 'toggle']);
    });
});

Route::get('/about', function () {
    return Inertia::render('About');
})->middleware(['auth', 'verified'])->name('about');
