<?php


use App\Http\Controllers\ShippingCostController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SupplyOrderController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\OperationController;

use App\Http\Controllers\MembershipFeeController;

use App\Http\Controllers\CartController;

use App\Http\Controllers\StatisticsController;

use App\Models\Product;





/* ----- PUBLIC ROUTES ----- */
Route::redirect('/', 'login')->name('home');

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
Route::middleware(['auth', 'verified', \App\Http\Middleware\CheckIfUserBlocked::class])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    Route::get('/card', [CardController::class, 'show'])->name('card.show');
    Route::get('/card/create', [CardController::class, 'create'])->name('card.create');
    Route::post('/card/create', [CardController::class, 'store'])->name('card.store');
    Route::post('/card/update', [CardController::class, 'update'])->name('balance.update');

    //Route::post('/orders', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('/operations', [OperationController::class, 'index'])->name('operations.index');

	Route::get('statistics/basic', [StatisticsController::class, 'basic'])->name('statistics.basic');
	Route::get('statistics/advanced', [StatisticsController::class, 'advanced'])->name('statistics.advanced');
	Route::get('statistics/export/sales-by-category', [StatisticsController::class, 'exportSalesByCategory'])->name('statistics.export.category');
	Route::get('statistics/export/user-spending', [StatisticsController::class, 'exportUserSpending'])->name('statistics.export.user_spending');
});




/* ----- AUTHENTICATED USERS (verificados ou não) ----- */
Route::middleware(['auth', \App\Http\Middleware\CheckIfUserBlocked::class])->group(function () {
     Route::resource('users', UserController::class);
//     Route::get('/users', [UserController::class, 'index'])->name('users.index')
//     ->can('viewAny-user');

// Route::get('/users/{user}', [UserController::class, 'show']) ->name('users.show')
//     ->can('view-user', 'user');

    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('shippingcosts', ShippingCostController::class);
    Route::resource('orders', OrderController::class);
    Route::resource('supplyorders', SupplyOrderController::class);
    Route::resource('membershipfees', MembershipFeeController::class)->except(['show']);
    
    Route::post('categories/{id}/restore', [CategoryController::class, 'restore'])->name('categories.restore');

    //Route::get('card', [CardController::class, 'showUserCard'])->name('card.show');
    Route::post('/membershipfees/{membershipfee}/pay', [MembershipFeeController::class, 'pay'])
    ->name('membershipfees.pay');

    
    Route::get('card', [CardController::class, 'show'])->name('card.show');






    Route::patch('/users/{user}/toggle-blocked', [UserController::class, 'toggleBlocked'])->name('users.toggleBlocked');
   
    Route::post('users/{user}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::patch('products/{product}/stock', [ProductController::class, 'updateStock'])
        ->name('products.updateStock');

    Route::get('products/trashed', [ProductController::class, 'trashed'])
        ->name('products.trashed');
    Route::post('products/{product}/restore', [ProductController::class, 'restore'])
        ->name('products.restore');

    Route::delete('/users/{user}/force', [UserController::class, 'forceDestroy'])->name('users.forceDestroy');
    Route::delete('/category/{category}/force', [CategoryController::class, 'forceDestroy'])->name('categories.forceDestroy');
    Route::delete('/product/{product}/force', [UserController::class, 'forceDestroy'])->name('products.forceDestroy');


    
    Route::get('/stockadjustments', [StockAdjustmentController::class, 'index'])->name('stockadjustments.index');
});

Route::post('shippingcosts/{shippingcost}/restore', [ShippingCostController::class, 'restore'])
    ->name('shippingcosts.restore');


/* ----- NON-VERIFIED USERS PUBLIC ROUTES----- */

Route::resource('products', ProductController::class)->only(['index', 'show']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('web')->name('dashboard');





require __DIR__ . '/auth.php';


