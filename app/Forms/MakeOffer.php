<?php

namespace Gigtrooper\Forms;

class MakeOffer extends BaseForm
{
    public $rate;

    public function getName()
    {
        return "Make Offer";
    }

    public function getType()
    {
        return "makeOffer";
    }

    public function getTitle()
    {
        $type = $this->getType();

        $offer = \Request::input($type);

        $rate = $offer['rate'] ?? null;

        $currency = $this->getCurrency();

        $this->rate = $rate;

        return "Service Fee Offer is $currency" . $rate;
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
        $this->isDisplay = false;

        if ($toFirstId == $currentUser->id) {
            $this->isDisplay = true;
        }

        return $this->isDisplay;
    }

    public function getHtml()
    {
        if (!$this->isDisplay()) return $this->elseHtml();

        $type = $this->getType();

        $currency = $this->getCurrency();
        return view('forms.make-offer',[
            'type'    => $type,
            'currency' => $currency
        ]);
    }

    private function elseHtml()
    {
        $name = $this->quoteMessage->to->name;

        return view('forms.waiting',[
            'title'    => "Waiting for $name to make an Offer Fee."
        ]);
    }

    public function afterSend($quoteMessage)
    {
        $source = $quoteMessage->source;

        $source->setAttribute('offerFee', $this->rate);

        $source->save();
    }
}