<?php


use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\Course;




/* ----- PUBLIC ROUTES ----- */
Route::get('/', function () {return view('welcome');})->name('home');

Route::get('products/showcase', [ProductController::class, 'showCase'])->name('products.showcase')
    ->can('viewShowCase', Product::class);


/* ----- VERIFIED USERS ONLY ----- */
Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});




/* ----- AUTHENTICATED USERS (verificados ou não) ----- */
Route::middleware(['auth'])->group(function () {
    Route::resource('courses', CourseController::class);
    Route::resource('users', UserController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);


    Route::patch('/users/{user}/toggle-blocked', [UserController::class, 'toggleBlocked'])->name('users.toggleBlocked');
    Route::delete('/users/{user}/force', [UserController::class, 'forceDestroy'])->name('users.forceDestroy');
    Route::post('users/{user}/restore', [UserController::class, 'restore'])->name('users.restore');

    Route::delete('/category/{category}/force', [UserController::class, 'forceDestroy'])->name('categories.forceDestroy');
    Route::delete('/product/{product}/force', [UserController::class, 'forceDestroy'])->name('products.forceDestroy');

});



/* ----- NON-VERIFIED USERS ----- */
Route::middleware(['auth'])->group(function () {
    Route::resource('products', ProductController::class)->only(['index', 'show']);

});


require __DIR__ . '/auth.php';


