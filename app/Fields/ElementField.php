<?php

namespace Gigtrooper\Fields;

use Gigtrooper\Models\BaseModel;

class ElementField extends BaseField
{

    public function getName()
    {
        return "Element";
    }

    public function getInputHtml($name, $params = [])
    {
        return '';
    }

    public function getValue()
    {
        $value = '';

        try {
            if (\Request::old('fields') != null) {
                $oldFields = \Request::old('fields');
                $fieldHandle = $this->settings['handle'];

                if (isset($oldFields[$fieldHandle])) {
                    $value = $oldFields[$fieldHandle];
                }
            } elseif ($this->element->getModel() != null) {
                $value = $this->element->getModel()->getAttribute($this->settings['handle']);
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
        }

        return $value;
    }

    public function getMatchesTax()
    {
        return '';
    }

    public function getReturnTax()
    {
        return '';
    }

    public function getWhereCql($value)
    {
        $handle = $this->settings['handle'];

        return "element.$handle $value";
    }

    public function getOrderTax()
    {
        return '';
    }

    public function getOrderQuery($order)
    {
        $handle = $this->settings['handle'];

        return "element.$handle $order";
    }

    public function save($handle, $fieldValue, BaseModel &$fromModel)
    {
        if ($handle == 'id' || !empty($this->settings['disabled'])) {
            return;
        }

        $fromModel->setAttribute($handle, $fieldValue);

        // Important to return $fromModel->save() to $fromModel variable to assign back to the loop saving elements.
        $fromModel = $fromModel->save();
    }

    public function getModelFieldValue(BaseModel $model, $handle, $params)
    {
        return $model->getAttribute($handle);
    }
}