<?php

namespace Gigtrooper\Fields;


class PlaintextField extends ElementField
{

    public function getName()
    {
        return "Plaintext";
    }

    public function getInputHtml($name, $params = [])
    {
        $value = $this->getValue();

        if (empty($value) && isset($this->settings['defaultValue'])) {
            $value = $this->settings['defaultValue'];
        }

        return view('fields.plaintext', [
            'name' => $name,
            'value' => $value,
            'params' => $params,
            'title' => (!empty($this->settings['title'])) ? $this->settings['title'] : ucwords($name)
        ]);
    }

    public function getSearchHtml()
    {
        $fieldHandle = $this->settings['handle'];
        $requestValues = $this->getRequestValues();

        return view('fields.plaintext', [
            'name' => $fieldHandle,
            'value' => ($requestValues)? $requestValues[0] : '',
            'title' => $this->getTitle(),
            'key' => 'filters'
        ]);
    }

    public function getOperatorValues($value, $valueKey = '', $operator)
    {
        $settingOperator = $this->settings['operator'] ?? null;
       if ($settingOperator && $settingOperator == 'like') {
           return "=~ '(?i).*$value.*'";
       }

       return parent::getOperatorValues($value, $valueKey, $operator);
    }
}