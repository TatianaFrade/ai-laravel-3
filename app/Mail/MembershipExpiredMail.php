<?php

// App\Mail\MembershipExpiredMail.php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MembershipExpiredMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('A sua adesão expirou')
                    ->view('emails.expired_membership')
                    ->with([
                        'user' => $this->user,
                        'appName' => config('app.name')
                    ]);
    }
}
