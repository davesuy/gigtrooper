<?php

namespace Gigtrooper\Services;

use Gigtrooper\Forms\AcceptOffer;
use Gigtrooper\Forms\BaseForm;
use Gigtrooper\Forms\ConfirmEvent;
use Gigtrooper\Forms\MakeOffer;
use Gigtrooper\Forms\UpcomingEvent;

class MessageForm
{
    /**
     * @param $type
     *
     * @return BaseForm
     */
   public function getForm($type)
   {
        $forms = $this->getAllForms();

        foreach ($forms as $form) {
            if ($type == $form->getType()) {
                return $form;
            }
        }
   }

   public function getAllForms()
   {
        return [
          new MakeOffer(),
          new AcceptOffer(),
          new ConfirmEvent(),
          new UpcomingEvent()
        ];
   }


   public function getSequenceByType($type)
   {
       $sequences = $this->getSequences();

       return $sequences[$type] ?? null;
   }

   public function getSequences()
   {
       return [
         'requestQuote' => 'makeOffer',
         'makeOffer' => 'acceptOffer',
         'acceptOffer' => 'confirmEvent',
         'confirmEvent' => 'upcomingEvent'
       ];
   }
}