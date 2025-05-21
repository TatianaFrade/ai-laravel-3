<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('livewire.settings.profile');


    }

    public function membershipStatus()
    {
        $user = Auth::user();
        return view('membership.status', compact('user'));
    }

    public function showPaymentPage()
    {
        $user = Auth::user();

        if(!$user->email_confirmed){
            return redirect()->route('profile.edit')->with('error','Por favor, confirme o seu email antes de pagar.');
        }

         if (!$user->card) {
            $virtualCard = $this->createVirtualCardForUser($user);
        } else {
            $virtualCard = $user->card;
        }

        return view('membership.payment', compact('virtualCard'));
    }


    public function processPayment(Request $request)
    {
        $user = Auth::user();
        
        $paymentSuccess = true;

        if($paymentSuccess){
            $user->membership_active = true;
            $user->save();

            return redirect()->route('membership.status')->with('success', 'Pagamento realizado com sucesso! A associação está ativa');
        }
        return back()->with('error','Falha no pagamento. Tente novamente');

    }

    private function createVirtualCardForUser($user)
    {
        return Card::create([
            'user_id' => $user->id,
            'card_number' => 'VIRT' . strtoupper(\Illuminate\Support\Str::random(12)),
            'expiration_date' => now()->addYear(),
        ]);
    }




}