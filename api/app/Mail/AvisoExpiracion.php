<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AvisoExpiracion extends Mailable
{
    use Queueable, SerializesModels;

    public $socio;

    public function __construct($socio)
    {
        $this->socio = $socio;
    }

    public function build()
    {
        return $this->subject('Tu suscripción está por vencer')
                    ->view('emails.aviso_expiracion');
    }
}
