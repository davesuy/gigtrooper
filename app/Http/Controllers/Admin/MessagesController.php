<?php

namespace Gigtrooper\Http\Controllers\Admin;

use Gigtrooper\Forms\BaseForm;
use Gigtrooper\Http\Controllers\Controller;
use Gigtrooper\Models\Message;
use Gigtrooper\Traits\QuoteAble;

class MessagesController extends Controller
{
    use QuoteAble;

    public function index()
    {
        $currentUser = \Auth::user();

        $user = $currentUser->getModel();

        $messageChain = \App::make('messageChainService');

        $messages = $messageChain->getMessages($user);

        return view('admin.messages', [
            'messages' => $messages
        ]);
    }

    public function send($id)
    {
        $messageChain = \App::make('messageChainService');

        $type = \Request::input('type');

        $quoteMessage = $messageChain->getQuoteMessage($id);
        $firstMessage = $messageChain->getFirstMessage($id);

        $messageForm = \App::make('messageFormService');

        /**
         * @var $sendForm BaseForm
         */
        $sendForm = $messageForm->getForm($type);
        $sendForm->setQuoteMessageModel($firstMessage);
        $message = new Message();
        $message->title = $sendForm->getTitle();
        $message->body = $sendForm->getBody();
        $message->time = time();
        $message->type = $sendForm->getType();
        $message->read = "no";
        $quoteMessage->message = $message;
        $res = $messageChain->sendMessage($quoteMessage);

        $sendForm->afterSend($quoteMessage);
/*        if (!empty($tos)) {
            foreach ($tos as $key => $to) {
                $message = new Message();
                $message->title = $text;
                $message->time = (string) time();
                $message->type = 'requestQuote';
                $message->read = "no";

                $quoteMessage = new QuoteMessage();
                $quoteMessage->from = $fromModel;
                $quoteMessage->to = $to;
                $quoteMessage->source = Quote::find($id);
                $quoteMessage->message = $message;

                $res = $messageChain->sendMessage($quoteMessage);
            }
        }*/
        return redirect()->back()->with('status', 'Message sent');
    }
}
