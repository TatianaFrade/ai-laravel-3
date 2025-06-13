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
use App\Models\User;




// Test route to verify the fix for board/employee membership fee requirement
Route::get('test-membership-check', function () {
    $board561 = User::find(561);
    if ($board561) {
        echo 'Board User #561: ' . $board561->name . ' (Type: ' . $board561->type . ')' . "<br>";
        echo 'Has Paid Membership: ' . ($board561->hasPaidMembership() ? 'Yes' : 'No') . "<br>";
        echo 'Membership Expired: ' . ($board561->isMembershipExpired() ? 'Yes' : 'No') . "<br>";
        
        // Simulate the CartController logic
        if (!$board561->hasPaidMembership()) {
            echo 'CHECKOUT RESULT: Redirect to membership payment due to never having paid.';
        } elseif ($board561->isMembershipExpired()) {
            echo 'CHECKOUT RESULT: Redirect to membership payment due to expired membership.';
        } else {
            echo 'CHECKOUT RESULT: Proceed with order.';
        }
    } else {
        echo 'User not found.';
    }
});


/* ----- PUBLIC ROUTES ----- */
Route::redirect('/', 'login')->name('home');

Route::get('products/showcase', [ProductController::class, 'showCase'])->name('products.showcase')
    ->withoutMiddleware(['auth']);

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

    // Statistics routes - restricted to staff members only
    Route::middleware('can:staff')->group(function () {
        Route::get('statistics/basic', [StatisticsController::class, 'basic'])->name('statistics.basic');
        Route::get('statistics/advanced', [StatisticsController::class, 'advanced'])->name('statistics.advanced');
        Route::get('statistics/export/sales-by-category', [StatisticsController::class, 'exportSalesByCategory'])->name('statistics.export.category');
        Route::get('statistics/export/user-spending', [StatisticsController::class, 'exportUserSpending'])->name('statistics.export.user_spending');
    });
});




/* ----- AUTHENTICATED USERS (verificados ou não) ----- */
Route::middleware(['auth', \App\Http\Middleware\CheckIfUserBlocked::class])->group(function () {
    // User management routes - using the gates for access control
    Route::get('/users', [UserController::class, 'index'])
        ->name('users.index')
        ->middleware(['can:staff']);
        
    Route::get('/users/create', [UserController::class, 'create'])
        ->name('users.create')
        ->middleware(['can:board']);
        
    Route::post('/users', [UserController::class, 'store'])
        ->name('users.store')
        ->middleware(['can:board']);
        
    Route::get('/users/{user}', [UserController::class, 'show'])
        ->name('users.show')
        ->middleware(['can:staff']);
        
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])
        ->name('users.edit')
        ->middleware(['can:edit-user,user']);
        
    Route::put('/users/{user}', [UserController::class, 'update'])
        ->name('users.update')
        ->middleware(['can:edit-user,user']);
        
    Route::patch('/users/{user}', [UserController::class, 'update'])
        ->name('users.update')
        ->middleware(['can:edit-user,user']);
        
    Route::delete('/users/{user}', [UserController::class, 'destroy'])
        ->name('users.destroy')
        ->middleware(['can:delete-user,user']);

    // Category routes
    Route::resource('categories', CategoryController::class)->middleware('can:board');
    Route::post('categories/{id}/restore', [CategoryController::class, 'restore'])->name('categories.restore')->middleware('can:board');

    // Product routes
    Route::resource('products', ProductController::class)->except(['index', 'show'])->middleware('can:staff');
    Route::post('products/{id}/restore', [ProductController::class, 'restore'])->name('products.restore')->middleware('can:staff');
    Route::patch('products/{product}/stock', [ProductController::class, 'updateStock'])->name('products.updateStock')->middleware('can:staff');

    // Shipping costs routes
    Route::resource('shippingcosts', ShippingCostController::class)->middleware('can:staff');
    
    // Orders routes - All authenticated users can view orders (filtering done in controller)
    Route::resource('orders', OrderController::class);
    
    // Supply orders routes
    Route::resource('supplyorders', SupplyOrderController::class)->middleware('can:staff');
    
    // Membership fees routes - All authenticated users can access
    Route::resource('membershipfees', MembershipFeeController::class)->except(['show']);

    //Route::get('card', [CardController::class, 'showUserCard'])->name('card.show');
    Route::post('/membershipfees/{membershipfee}/pay', [MembershipFeeController::class, 'pay'])
    ->name('membershipfees.pay');

    
    Route::get('card', [CardController::class, 'show'])->name('card.show');


    



    Route::patch('/users/{user}/toggle-blocked', [UserController::class, 'toggleBlocked'])
        ->name('users.toggleBlocked')
        ->middleware('can:board');
   
    Route::post('users/{user}/restore', [UserController::class, 'restore'])
        ->name('users.restore')
        ->middleware('can:board');
        
    // This route is duplicated - already defined above
    /* Route::patch('products/{product}/stock', [ProductController::class, 'updateStock'])
        ->name('products.updateStock'); */

    Route::get('products/trashed', [ProductController::class, 'trashed'])
        ->name('products.trashed')
        ->middleware('can:staff');
        
    // This route is duplicated - already defined above
    /* Route::post('products/{product}/restore', [ProductController::class, 'restore'])
        ->name('products.restore'); */

    Route::delete('/users/{id}/force', [UserController::class, 'forceDestroy'])
        ->name('users.forceDestroy')
        ->middleware('can:board');
        
    Route::delete('/category/{category}/force', [CategoryController::class, 'forceDestroy'])
        ->name('categories.forceDestroy')
        ->middleware('can:board');
        
    Route::delete('/product/{id}/force', [ProductController::class, 'forceDestroy'])
        ->name('products.forceDestroy')
        ->middleware('can:staff');


    
    Route::get('/stockadjustments', [StockAdjustmentController::class, 'index'])
        ->name('stockadjustments.index')
        ->middleware('can:staff');
});

Route::post('shippingcosts/{shippingcost}/restore', [ShippingCostController::class, 'restore'])
    ->name('shippingcosts.restore')
    ->middleware('can:staff');


/* ----- NON-VERIFIED USERS PUBLIC ROUTES----- */

// Define public product routes explicitly without auth requirement
Route::get('products', [ProductController::class, 'index'])->name('products.index')->withoutMiddleware(['auth']);
Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show')->withoutMiddleware(['auth']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('web')->name('dashboard');





require __DIR__ . '/auth.php';


