<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Course;
use Illuminate\Support\Facades\Gate; 
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    

    public function register(): void
    {
        
    }

    public function boot(): void
    {
        Gate::define('board', function (User $user) { 
            return $user->type === 'board';
        }); 

        Gate::define('employee', fn(User $user) => $user->type === 'employee');

        Gate::define('manage-users', function (User $user) {
            return in_array($user->type, ['board', 'employee']);
        });




        Gate::define('viewAny-user', function (User $user) {
            return in_array($user->type, ['employee', 'board']);
        });

        Gate::define('view-user', function (User $user, User $userToView) {
            return in_array($user->type, ['employee', 'board']);
        });

      



        Gate::define('update-profile', function ($user, $targetUser = null) {
            return $user->isBoard() || $user->isRegular();
        });

        Gate::define('see-obfuscated-order-id', function (User $user) {
        return $user->type === 'member';
    });




        
    }
}
