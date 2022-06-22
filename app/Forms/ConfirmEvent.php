<?php

namespace Gigtrooper\Forms;

class ConfirmEvent extends BaseForm
{
    private $answer;

    public function getName()
    {
        return "Confirm Event";
    }

    public function getType()
    {
        return "confirmEvent";
    }

    public function getTitle()
    {
        $type = $this->getType();

        $offer = \Request::input($type);

        $answer = $offer['answer'] ?? null;

        $name = $this->quoteMessage->to->name;

        if ($answer == 'Y') {
            return $name . ' has CONFIRMED the event.';
        }
        $this->answer = $answer;
        return $name . ' has DECLINED the event.';
    }

    public function getBody()
    {
        $type = $this->getType();

        $body = \Request::input($type);

        $details = $body['details'] ?? null;

        return $details;
    }

    public function isDisplay()
    {
        // Allow override isDisplay
        if ($this->isDisplay !== null) {
            return $this->isDisplay;
        }

        $toUser = $this->quoteMessage->to;

        $toFirstId = $toUser->id;

        $currentUser = \Auth::user();

        if ($toFirstId == $currentUser->id) {
            $this->isDisplay = true;
        }

        return $this->isDisplay;
    }

    public function getHtml()
    {
        if (!$this->isDisplay()) return $this->elseHtml();

        $type = $this->getType();

        $source = $this->quoteMessage->source;

        $name = $this->quoteMessage->to->name;

        $acceptOffer = $source->acceptOffer ?? null;
        if ($acceptOffer != null && $acceptOffer == 'N') {
            $this->isDisplay = false;
            $title = "Sorry $name did not ACCEPT your offer.";
            return view('forms.waiting',[
                'title' => $title
            ]);

        }

        $title = "Confirm Event? <small>Choose yes if you are confirming to service the event and no if not)</small>";
        return view('forms.yes-no',[
            'type'  => $type,
            'title' => $title
        ]);
    }

    private function elseHtml()
    {
        $name = $this->quoteMessage->to->name;

        $source = $this->quoteMessage->source;

        $acceptOffer = $source->acceptOffer ?? null;

        if ($acceptOffer != null && $acceptOffer == 'N') {
            $title = 'Request quote again from other service providers.';
        } else {
            $title = "Waiting for $name to confirm the event.";
        }

        return view('forms.waiting',[
            'title' => $title
        ]);
    }

    public function afterSend($quoteMessage)
    {
        $source = $quoteMessage->source;
        $source->confirmEvent = $this->answer;

        $source->save();
    }
}