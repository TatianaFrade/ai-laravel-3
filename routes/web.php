<?php


use App\Http\Controllers\ShippingCostController;
use App\Models\Order;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SupplyOrderController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\CardController;

use App\Http\Controllers\MembershipFeeController;

use App\Http\Controllers\CartController;

use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\Course;




/* ----- PUBLIC ROUTES ----- */
Route::get('/', function () {
    return view('welcome'); })->name('home');

Route::get('products/showcase', [ProductController::class, 'showCase'])->name('products.showcase')
    ->can('viewShowCase', Product::class);
Route::get('cart', [CartController::class, 'show'])->name('cart.show');
Route::post('cart/{product}', [CartController::class, 'addToCart'])->name('cart.add');
Route::delete('cart/{product}', [CartController::class, 'removeFromCart'])->name('cart.remove');
Route::post('cart', [CartController::class, 'confirm'])->name('cart.confirm');
Route::delete('cart', [CartController::class, 'destroy'])->name('cart.destroy');
Route::post('cart/{product}/increase', [CartController::class, 'increaseQuantity'])->name('cart.increase');
Route::post('cart/{product}/decrease', [CartController::class, 'decreaseQuantity'])->name('cart.decrease');

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
    Route::resource('users', UserController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('shippingcosts', ShippingCostController::class);
    Route::resource('orders', OrderController::class);
    Route::resource('supplyorders', SupplyOrderController::class);
    Route::resource('membershipfees', MembershipFeeController::class)->except(['show']);
    Route::get('card', [CardController::class, 'showUserCard'])->name('card.show');






    Route::patch('/users/{user}/toggle-blocked', [UserController::class, 'toggleBlocked'])->name('users.toggleBlocked');
   
    Route::post('users/{user}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::patch('products/{product}/stock', [ProductController::class, 'updateStock'])
    ->name('products.updateStock');



    Route::delete('/users/{user}/force', [UserController::class, 'forceDestroy'])->name('users.forceDestroy');
    Route::delete('/category/{category}/force', [UserController::class, 'forceDestroy'])->name('categories.forceDestroy');
    Route::delete('/product/{product}/force', [UserController::class, 'forceDestroy'])->name('products.forceDestroy');


    
    Route::get('/stockadjustments', [StockAdjustmentController::class, 'index'])->name('stockadjustments.index');
});



/* ----- NON-VERIFIED USERS PUBLIC ROUTES----- */

Route::resource('products', ProductController::class)->only(['index', 'show']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('web')->name('dashboard');





require __DIR__ . '/auth.php';


