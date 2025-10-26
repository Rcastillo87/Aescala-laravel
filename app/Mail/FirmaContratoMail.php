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
    public $callLink;


    public function __construct($linkContrato,  $nombre, $callLink = 'Firmar de Contrato')
    {
        $this->linkContrato = $linkContrato;
        $this->nombre = $nombre;
        $this->callLink = $callLink;
    }

    public function build()
    {
        return $this->subject($this->callLink . ' - ' . env('RAZON'))
                    ->view('emails.firmaContrato')
                    ->with([
                        'linkContrato' => $this->linkContrato,
                        'nombre' => $this->nombre,
                    ]);
    }
}
