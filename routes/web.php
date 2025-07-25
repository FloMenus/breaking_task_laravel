<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColumnController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IcalSynchronizationController;
use App\Http\Controllers\PriorityController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {

    Route::prefix('project')->name('project.')->group(function () {
        Route::get('/index', [ProjectController::class, 'index'])->name('index');
        Route::post('/store', [ProjectController::class, 'store'])->name('store');
        Route::get('/edit', [ProjectController::class, 'edit'])->name('edit');
        Route::post('/update', [ProjectController::class, 'update'])->name('update');
        Route::get('/delete', [ProjectController::class, 'delete'])->name('delete');
        Route::post('/destroy', [ProjectController::class, 'destroy'])->name('destroy');
        Route::get('/leave', [ProjectController::class, 'leave'])->name('leave');
        Route::post('/quit', [ProjectController::class, 'quit'])->name('quit');

        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [ProjectController::class, 'users'])->name('index');
            Route::post('/store', [ProjectController::class, 'storeUsers'])->name('store');
            Route::get('/search', [ProjectController::class, 'searchUsers'])->name('search');
            Route::get('/delete', [ProjectController::class, 'deleteUsers'])->name('delete');
            Route::post('/destroy', [ProjectController::class, 'destroyUsers'])->name('destroy');
        });

        Route::get('/{id}', [ProjectController::class, 'show'])->name('show');
        Route::get('/{id}/parameters', [ProjectController::class, 'parameters'])->name('parameters');
        Route::get('/{project}/calendar.ics', [IcalSynchronizationController::class, 'export'])
            ->name('ical');
    });

    Route::prefix('task')->name('task.')->group(function () {
        Route::get('/show', [TaskController::class, 'show'])->name('show');
        Route::get('/create', [TaskController::class, 'create'])->name('create');
        Route::post('/store', [TaskController::class, 'store'])->name('store');
        Route::get('/edit', [TaskController::class, 'edit'])->name('edit');
        Route::post('/update', [TaskController::class, 'update'])->name('update');
        Route::get('/delete', [TaskController::class, 'delete'])->name('delete');
        Route::post('/destroy', [TaskController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('column')->name('column.')->group(function () {
        Route::get('/show', [ColumnController::class, 'show'])->name('show');
        Route::get('/create', [ColumnController::class, 'create'])->name('create');
        Route::post('/store', [ColumnController::class, 'store'])->name('store');
        Route::get('/edit', [ColumnController::class, 'edit'])->name('edit');
        Route::post('/update', [ColumnController::class, 'update'])->name('update');
        Route::get('/delete', [ColumnController::class, 'delete'])->name('delete');
        Route::post('/destroy', [ColumnController::class, 'destroy'])->name('destroy');
        Route::post('/sort', [ColumnController::class, 'sort'])->name('sort');
    });

    Route::prefix('category')->name('category.')->group(function () {
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::post('/store', [CategoryController::class, 'store'])->name('store');
        Route::get('/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::post('/update', [CategoryController::class, 'update'])->name('update');
        Route::get('/delete', [CategoryController::class, 'delete'])->name('delete');
        Route::post('/destroy', [CategoryController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('priority')->name('priority.')->group(function () {
        Route::get('/create', [PriorityController::class, 'create'])->name('create');
        Route::post('/store', [PriorityController::class, 'store'])->name('store');
        Route::get('/edit', [PriorityController::class, 'edit'])->name('edit');
        Route::post('/update', [PriorityController::class, 'update'])->name('update');
        Route::get('/delete', [PriorityController::class, 'delete'])->name('delete');
        Route::post('/destroy', [PriorityController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('calendar')->name('calendar.')->group(function () {
        Route::get('/day-view/{project}', [CalendarController::class, 'dayView'])->name('day-view');
        Route::get('/threedays-view/{project}', [CalendarController::class, 'threeDaysView'])->name('threedays-view');
        Route::get('/week-view/{project}', [CalendarController::class, 'weekView'])->name('week-view');
        Route::get('/month-view/{project}', [CalendarController::class, 'monthView'])->name('month-view');
    });

    Route::prefix('notifications')->name('notifications.')->group(function() {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{id}', [NotificationController::class, 'read'])->name('read');
    });

});

Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['auth', 'signed'])
    ->name('verification.verify');

require __DIR__.'/auth.php';
