<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DisciplineController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ProfileController; 
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

    Route::middleware(['auth'])->group(function () {
        Route::redirect('settings', 'settings/profile');

        Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
        Volt::route('settings/password', 'settings.password')->name('settings.password');
        Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

        
});

   


    
Route::get('courses/showcase', [CourseController::class, 'showCase'])->name('courses.showcase');

// Route::get('users/index', [UserController::class, 'index'])->name('users.index');
// Route::get('users/create', [UserController::class, 'create'])->name('users.create');
// Route::get('users/show', [UserController::class, 'show'])->name('users.show');
// Route::get('users/update', [UserController::class, 'update'])->name('users.update');
// Route::get('users/edit', action: [UserController::class, 'edit'])->name('users.edit');
// Route::get('users/destroy', action: [UserController::class, 'destroy'])->name('users.destroy');




Route::resource('courses', CourseController::class);

Route::resource('users', UserController::class);



require __DIR__.'/auth.php';

