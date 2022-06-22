<?php

namespace Gigtrooper\Fields;

class DropdownElementField extends ElementField
{
    public function getName()
    {
        return "DropdownElement";
    }

    public function getInputHtml($name, $params = [])
    {
        $value = $this->getValue();

        $settingOptions = $this->settings['options'] ?? null;

        $options = [];

        if ($settingOptions) {
            foreach ($settingOptions as $settingOption) {
                $selected = ($value == $settingOption);
                $options[] = new OptionData($settingOption, $settingOption, $selected);
            }
        }

        return view('fields.dropdown', [
            'name' => $name,
            'title' => $this->getTitle(),
            'options' => $options
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
        return "=~ '(?i).*$value.*'";
    }
}