<?php

namespace Gigtrooper\Mail;

use Gigtrooper\Models\BaseModel;
use Gigtrooper\Services\MessageChainService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailQuote extends Mailable
{
    use Queueable, SerializesModels;
    public $quoteMessage;
    public $toNode;

    public function __construct(BaseModel $quoteMessage, $toNode)
    {
        $this->quoteMessage = $quoteMessage;
        $this->toNode = $toNode;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function build()
    {

        /** @var  $messageChain MessageChainService */
        $messageChain = \App::make('messageChainService');

        //$this->quoteMessag$body = $messageChain->getQuoteInfo($this->source)->quoteText();
        $body = $this->quoteMessage->message->body;
        $title = $this->quoteMessage->message->title;

        return $this->subject($title)->view('emails.quote-message', [
            'body' =>  $body,
            'source' =>  $this->quoteMessage->source,
            'to' =>  $this->toNode
        ]);
    }
}