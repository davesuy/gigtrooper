<?php

namespace Gigtrooper\Forms;

use Gigtrooper\Models\QuoteMessage;

abstract class BaseForm
{
    protected $quoteMessage;
    protected $isDisplay;

    abstract public function getName();
    abstract public function getType();
    abstract public function getHtml();
    abstract public function getTitle();
    abstract public function getBody();

    public function setQuoteMessageModel(QuoteMessage $quoteMessage)
    {
        $this->quoteMessage = $quoteMessage;
    }

    public function getCurrency()
    {
        $fieldTypesService = \App::make('fieldTypes');
        $fields = $fieldTypesService->getFieldsByHandles(['Country']);
        $fromModel = $this->quoteMessage->from;
        $fromModel->setFieldTypes($fields);

        $code = 'PH';
        $countries = $fromModel->getFieldValue('Country');
        if (!empty($countries)) {
            $code = $countries[0]->countryCode;
        }

        return \App::make('countryService')->getCountryCurrency($code);
    }

    /**
     * @return string
     */
    public function displayHtml()
    {
        $type = $this->getType();
        $html = "<input type='hidden' name='type' value='$type' />";
        $html.= $this->getHtml();

        if ($this->isDisplay) {
            $html.= "<div class='send-button'>
                  <button class='btn btn-primary btn-large' id='btn-chat'>Send</button>
                </div>";
        }

        return $html;
    }

    public function afterSend($quoteMessage)
    {
        return null;
    }
}