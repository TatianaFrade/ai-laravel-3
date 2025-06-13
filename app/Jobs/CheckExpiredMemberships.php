<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\MembershipExpiring;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class CheckExpiredMemberships implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
    }

    public function handle(): void
    {
        // Find users whose membership is expiring in 7 days
        $expiringUsers = User::where('type', 'member')
            ->whereNotNull('membership_expiration_date')
            ->where('membership_expiration_date', '>', now())
            ->where('membership_expiration_date', '<=', now()->addDays(7))
            ->get();

        foreach ($expiringUsers as $user) {
            $user->notify(new MembershipExpiring($user->membership_expiration_date));
        }

        // Block users whose membership has expired
        User::where('type', 'member')
            ->whereNotNull('membership_expiration_date')
            ->where('membership_expiration_date', '<', now())
            ->where('blocked', 0)
            ->update(['blocked' => 1]);
    }
}
