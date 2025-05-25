<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;

class OrderCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $pdfPath;

    public function __construct(Order $order, string $pdfPath)
    {
        $this->order = $order;
        $this->pdfPath = $pdfPath;
    }

    public function build()
    {


        return $this->subject('Encomenda Concluída')
                    ->markdown('emails.orders.completed')
                    ->attach($this->pdfPath, [
                        'as' => 'recibo_' . $this->order->id . '.pdf',
                        'mime' => 'application/pdf',
                    ]);
    }
}


 


