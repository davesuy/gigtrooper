<?php

namespace Gigtrooper\Traits;

use Everyman\Neo4j\Node;
use Gigtrooper\Elements\QuoteElement;
use Gigtrooper\Forms\BaseForm;
use Gigtrooper\Models\Message;
use Gigtrooper\Models\Quote;
use Gigtrooper\Models\User;

trait QuoteAble
{
    public function view($id)
    {
        $messageChain = \App::make('messageChainService');

        $messages = $messageChain->getMessage($id);

        $quoteText = '';
        $quoteModel = null;
        $sendForm = null;

        if (!empty($messages)) {
            foreach ($messages as $key => $message) {
                $fromNode = $message['from'];
                $toNode = $message['to'];

                $messages[$key]['from'] = $this->getUserModel($fromNode);
                $messages[$key]['to']   = $this->getUserModel($toNode);
            }

            $quoteModel = new Quote();
            $quoteModel->setAttributes($messages[0]['quoteProperties']);

            $firstMessage = $messages[0]['messages'];

            $messageChain = \App::make('messageChainService');

            $firstMessageModel = new Message();
            $firstMessageModel->setAttributes($firstMessage->getProperties());

            $quoteMessage = $messageChain->getQuoteMessage($id);
            $firstQuoteMessage = $messageChain->getFirstMessage($id);

            $quoteMessage->firstMessage = $firstMessageModel;
            $messageForm = \App::make('messageFormService');

            $type = $firstMessage->type;

            $sequenceType = $messageForm->getSequenceByType($type);

            //$sequenceType = 'upcomingEvent';
            /**
             * @var $sendForm BaseForm
             */
            $sendForm = $messageForm->getForm($sequenceType);

            if ($sendForm) {
                $sendForm->setQuoteMessageModel($firstQuoteMessage);
            }
        }

        if ($quoteModel) {
            $quoteText = $messageChain->getQuoteInfo($quoteModel)->quoteText();
        }

        $currentUser = \Auth::user();

        $length = count($messages) - 1;

        $user = \Auth::getUser();

        if (in_array('superAdmin', $user->roles) || in_array('administrator', $user->roles)) {
            $element = $this->getElement($id);
            \Field::setElement($element);
        }

        return view('admin.messages-view', [
            'quoteId'   => $id,
            'messages'  => $messages,
            'length'    => $length,
            'quoteText' => $quoteText,
            'sendForm' => $sendForm,
            'currentUserId' => $currentUser->id
        ]);
    }

    public function getUserModel(Node $node)
    {
        $properties = $node->getProperties();
        $model = new User();
        $model->setAttributes($properties);

        return $model;
    }

    public function getElement($id)
    {
        $fieldTypesService = \App::make('fieldTypes');

        $fieldTypes = $fieldTypesService->getFieldsByHandles(['eventStatus']);

        $element = new QuoteElement();

        $element->setFieldTypes($fieldTypes);

        if ($id) {
            $model = Quote::find($id);

            $element->setModel($model);
        }

        return $element;
    }
}