<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FirmaContratoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $linkContrato;
    public $nombre;

    public function __construct($linkContrato,  $nombre)
    {
        $this->linkContrato = $linkContrato;
        $this->nombre = $nombre;
    }

    public function build()
    {
        return $this->subject('Firma de Contrato - ' . env('RAZON'))
                    ->view('emails.firmaContrato')
                    ->with([
                        'linkContrato' => $this->linkContrato,
                        'nombre' => $this->nombre,
                    ]);
    }
}
