<?php

namespace App\Mail;

use App\Models\Servicio;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ServicioTerminadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Servicio $servicio) {}

    public function build(): self
    {
        return $this->subject('Tu moto ya está lista - Taller JEJ')
            ->view('emails.servicio-terminado');
    }
}
