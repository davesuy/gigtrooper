<?php

namespace Gigtrooper\Forms;

class AcceptOffer extends BaseForm
{
    private $answer;

    public function getName()
    {
        return "Accept Offer";
    }

    public function getType()
    {
        return "acceptOffer";
    }

    public function getTitle()
    {
        $type = $this->getType();

        $offer = \Request::input($type);

        $answer = $offer['answer'] ?? null;

        $name = $this->quoteMessage->from->name;
        $this->answer = $answer;
        if ($answer == 'Y') {
            return $name . ' has ACCEPTED your fee offer';
        }

        return $name . ' has REFUSED your fee offer';
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
        $toUser = $this->quoteMessage->to;

        $toFirstId = $toUser->id;

        $currentUser = \Auth::user();

        if ($toFirstId != $currentUser->id) {
            $this->isDisplay = true;
        }

        return $this->isDisplay;
    }

    public function getHtml()
    {
        if (!$this->isDisplay()) return $this->elseHtml();

        $type = $this->getType();

        $title = "Accept Offer Fee? <small>(Choose Yes if you agree with the offer and No if not)</small>";

        return view('forms.yes-no',[
            'type'  => $type,
            'title' => $title
        ]);
    }

    private function elseHtml()
    {
        $name = $this->quoteMessage->from->name;

        return view('forms.waiting',[
            'title'    => "Waiting for $name to accept your offer fee."
        ]);
    }

    public function afterSend($quoteMessage)
    {
        $source = $quoteMessage->source;

        $source->setAttribute('acceptOffer', $this->answer);

        $source->save();
    }
}