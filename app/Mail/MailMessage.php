<?php

namespace Gigtrooper\Mail;

use Gigtrooper\Models\Message;
use Gigtrooper\Models\QuoteMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailMessage extends Mailable
{
    use Queueable, SerializesModels;
    public $quoteMessage;
    public $to;

    public function __construct(QuoteMessage $quoteMessage, $toNode)
    {
        $this->quoteMessage = $quoteMessage;
        $this->toNode      = $toNode;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function build()
    {
        return $this->subject($this->quoteMessage->message->title)->view('emails.message', [
            'quoteMessage' =>  $this->quoteMessage,
            'to'      =>  $this->toNode
        ]);
    }
}