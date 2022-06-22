<?php

namespace Gigtrooper\Fields;


use Gigtrooper\Models\BaseModel;

class NumberField extends ElementField
{

    public function getName()
    {
        return "Number";
    }

    public function getInputHtml($name, $params = [])
    {
        $value = $this->getValue();

        if (empty($value) && isset($this->settings['defaultValue'])) {
            $value = $this->settings['defaultValue'];
        }

        return view('fields.number', [
            'name' => $name,
            'value' => $value,
            'params' => $params,
            'title' => $this->getTitle()
        ]);
    }

    public function save($handle, $fieldValue, BaseModel &$fromModel)
    {
        if ($handle == 'id' || !empty($this->settings['disabled'])) {
            return;
        }

        $fieldValue = (int)$fieldValue;

        $fromModel->setAttribute($handle, $fieldValue);

        // Important to return $fromModel->save() to $fromModel variable to assign back to the loop saving elements.
        $fromModel = $fromModel->save();
    }
}