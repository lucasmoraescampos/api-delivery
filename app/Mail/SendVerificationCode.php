<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendVerificationCode extends Mailable
{
    use Queueable, SerializesModels;

    private $code;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($code)
    {
        $this->code = $code;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('naoresponder@meupedido.org', 'naoresponder')
            ->subject('Meu Pedido - Código de acesso')
            ->view('mail.sendVerificationCode')
            ->with([
                'url' => env('APP_URL'),
                'code' => $this->code
            ]);
    }
}
