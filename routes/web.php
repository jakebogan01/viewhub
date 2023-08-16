<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\TaskControler;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store']);
Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth');

//Route::middleware('auth')->group(function () {
    Route::get('/', [TaskControler::class, 'index'])->name('tasks.index');
//    Route::get('/', function () {
//        return Inertia::render('Home', [
//            'frameworks' => ['Laravel', 'Inertia', 'Tailwind CSS', 'Vue']
//        ]);
//    });

    Route::get('/users', function () {
        sleep(1);

        return Inertia::render('Users/Index', [
            'time' => now()->format('h:i:s A'),
            'users' => User::query()
                ->when(Request::input('search'), function ($query, $search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->paginate(10)
                ->withQueryString()
                ->through(fn($user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'can' => [
                        'edit' => Auth::user()->can('edit', $user),
                    ],
                ]),

            'filters' => Request::only(['search']),
            'can' => [
                'createUser' => Auth::user()->can('create', User::class)
            ],
        ]);
    })->name('users');

    Route::get('/users/create', function () {
        return Inertia::render('Users/Create');
    })->can('create', 'App\Models\User');

    Route::post('/users', function () {
        $attributes = Request::validate([
            'name' => 'required',
            'email' => ['required', 'email'],
            'password' => 'required',
        ]);

        User::create($attributes);

        return to_route('users');
    });

    Route::get('/settings', function () {
        return Inertia::render('Settings');
    });

    Route::post('/test', function () {
        dd(request('foo'));
    });
//});
