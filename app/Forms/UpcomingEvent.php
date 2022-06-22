<?php

namespace Gigtrooper\Forms;

class UpcomingEvent extends BaseForm
{
    private $answer;

    public function getName()
    {
        return "Upcoming Event";
    }

    public function getType()
    {
        return "upcomingEvent";
    }

    public function getTitle()
    {
        $type = $this->getType();

        $toName = $this->quoteMessage->to->name;
        $fromName = $this->quoteMessage->from->name;

        return  "$fromName has an event with the service provider $toName.";
    }

    public function getBody()
    {
        return "";
    }

    public function isDisplay()
    {
        return true;
    }

    public function getHtml()
    {
        $toUser = $this->quoteMessage->to;

        $toFirstId = $toUser->id;

        $currentUser = \Auth::user();

        $title = "Thank you for booking the service provider with us. Enjoy your event.";

        if ($toFirstId == $currentUser->id) {
            $title = "Congratulations you have been booked! More Power.";
        }

        return view('forms.waiting',[
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