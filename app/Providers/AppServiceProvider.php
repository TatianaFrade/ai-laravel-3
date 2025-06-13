<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Course;
use Illuminate\Support\Facades\Gate; 
use App\Models\User;
use App\Models\Card;
use App\Models\Category;
use App\Models\MembershipFee;
use App\Models\Operation;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingCost;
use App\Models\StockAdjustment;
use App\Models\SupplyOrder;
use App\Policies\CardControllerPolicy;
use App\Policies\CategoryControllerPolicy;
use App\Policies\MembershipFeeControllerPolicy;
use App\Policies\OperationControllerPolicy;
use App\Policies\OrderControllerPolicy;
use App\Policies\ProductControllerPolicy;
use App\Policies\ShippingCostControllerPolicy;
use App\Policies\StockAdjustmentControllerPolicy;
use App\Policies\SupplyOrderControllerPolicy;
use App\Policies\UserControllerPolicy;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    

    public function register(): void
    {
        
    }

    public function boot(): void
    {
        // Register controller-based policies
        Gate::policy(Card::class, CardControllerPolicy::class);
        Gate::policy(Category::class, CategoryControllerPolicy::class);
        Gate::policy(MembershipFee::class, MembershipFeeControllerPolicy::class);
        Gate::policy(Operation::class, OperationControllerPolicy::class);
        Gate::policy(Order::class, OrderControllerPolicy::class);
        Gate::policy(Product::class, ProductControllerPolicy::class);
        Gate::policy(ShippingCost::class, ShippingCostControllerPolicy::class);
        Gate::policy(StockAdjustment::class, StockAdjustmentControllerPolicy::class);
        Gate::policy(SupplyOrder::class, SupplyOrderControllerPolicy::class);
        Gate::policy(User::class, UserControllerPolicy::class);
        
        // 1. Essential gates for user types - core gates
        Gate::define('board', function (User $user) { 
            return $user->type === 'board';
        }); 

        Gate::define('employee', function (User $user) {
            return $user->type === 'employee';
        });
        
        Gate::define('member', function (User $user) {
            return $user->type === 'member';
        });
        
        // Staff gate for combined board and employee permissions
        Gate::define('staff', function (User $user) {
            return in_array($user->type, ['board', 'employee']);
        });
        
        // 2. Special case gates that can't be easily replaced by the core gates
        Gate::define('edit-user', function (User $user, User $userToEdit) {
            if ($user->id === $userToEdit->id) {
                return false; // Users can't edit themselves
            }
            return $user->can('board');
        });
        
        Gate::define('delete-user', function (User $user, User $userToDelete) {
            return $user->can('board') && $user->id !== $userToDelete->id;
        });
        
        // Keep this gate since it's used in the front-end
        Gate::define('update-profile', function ($user, $targetUser = null) {
            return $user->can('board') || $user->isRegular(); 
        });
        
    }
}
