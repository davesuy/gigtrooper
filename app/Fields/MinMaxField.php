<?php

namespace Gigtrooper\Fields;

use Gigtrooper\Models\BaseModel;

class MinMaxField extends ElementField
{
    private $value = null;
    private $requestValues = [];

    public function getName()
    {
        return "MinMax";
    }

    public function getInputHtml($handle, $params = [])
    {
        $value = $this->getValue();

        return view('fields.minmax', [
            'handle' => $handle,
            'value' => $value,
            'title' => $this->getTitle(),
            'params' => $params,
            'step' => (!empty($this->settings['step'])) ? $this->settings['step'] : 0,
            'message' => (!empty($this->settings['message'])) ? $this->settings['message'] : '0 - 0'
        ]);
    }

    public function getValue()
    {
        $request = \Request::old('fields');
        $requestValues = $this->getRequestValues($request);

        if (!empty($requestValues)) {
            return $requestValues;
        }

        try {
            $handle = $this->settings['handle'];

            $minValue = 0;
            $maxValue = 0;

            if ($this->element->getModel() != null) {
                $min = $this->element->getModel()->getAttribute('min'.$handle);
                $max = $this->element->getModel()->getAttribute('max'.$handle);

                $minValue = (!empty($min)) ? $min : 0;
                $maxValue = (!empty($max)) ? $max : 0;
            }

            return ['min' => $minValue, 'max' => $maxValue];
        } catch (\Exception $e) {
        }
    }

    public function getSearchHtml()
    {
        $handle = $this->settings['handle'];

        $value = ['min' => 0, 'max' => 0];

        if (\Request::get('f') !== null) {
            $filterFields = \Request::get('f');

            if (isset($filterFields[$handle])) {
                $value = $filterFields[$handle];
                //$value = $this->getMinMaxValue($value);
            }
        }

        return view('fields.minmax-search', [
            'handle' => $handle,
            'key' => 'filters',
            'value' => $value,
            'title' => '',
            'settings' => $this->settings,
            'step' => (!empty($this->settings['step'])) ? $this->settings['step'] : 0,
            'message' => '0 - 0'
        ]);
    }

    public function save($handle, $fieldValue, BaseModel &$fromModel)
    {
        if (!empty($this->settings['disabled'])) {
            return;
        }

        $min = (int)$fieldValue['min'];
        $max = (int)$fieldValue['max'];

        $fromModel->setAttribute('min'.$handle, $min);
        $fromModel->setAttribute('max'.$handle, $max);

        // Important to return $fromModel->save() to $fromModel variable to assign back to the loop saving elements.
        $fromModel = $fromModel->save();
    }

    public function prepareWhereValue($handle, $value)
    {
        //$value = $this->getMinMaxValue($value);
        //dd($value);
        //$rangeWheres = array(
        //	"minLow$handle"  => (int) $value['min']['low'],
        //	"minHigh$handle" => (int) $value['min']['high'],
        //	"maxLow$handle"  => (int) $value['max']['low'],
        //	"maxHigh$handle" => (int) $value['max']['high']
        //);

        $this->requestValues = $value;

        $rangeWheres = [];

        if (isset($value['min'])) {
            if (isset($value['min']['low'])) {
                $rangeWheres["minLow$handle"] = (int)$value['min']['low'];
            }

            if (isset($value['min']['high'])) {
                $rangeWheres["minHigh$handle"] = (int)$value['min']['high'];
            }
        }

        if (isset($value['max'])) {
            if (isset($value['max']['low'])) {
                $rangeWheres["maxLow$handle"] = (int)$value['max']['low'];
            }

            if (isset($value['max']['high'])) {
                $rangeWheres["maxHigh$handle"] = (int)$value['max']['high'];
            }
        }

        return $rangeWheres;
    }

    public function getWhereCql($value)
    {
        $handle = $this->settings['handle'];

        $query = [];

        if (isset($this->requestValues['min'])) {
            $minQuery = [];

            if (isset($this->requestValues['min']['low'])) {
                $minQuery[] = "element.min$handle >= {minLow$handle}";
            }

            if (isset($this->requestValues['min']['high'])) {
                $minQuery[] = "element.min$handle <= {minHigh$handle}";
            }

            if (!empty($minQuery)) {
                $query[] = "(".implode(" AND ", $minQuery).")";
            }
        }

        if (isset($this->requestValues['max'])) {
            $maxQuery = [];

            if (isset($this->requestValues['max']['low'])) {
                $maxQuery[] = "element.max$handle >= {maxLow$handle}";
            }

            if (isset($this->requestValues['max']['high'])) {
                $maxQuery[] = "element.max$handle <= {maxHigh$handle}";
            }

            if (!empty($maxQuery)) {
                $query[] = "(".implode(" AND ", $maxQuery).")";
            }
        }

        if (!empty($query)) {
            return implode(" AND ", $query);
        } else {
            return "";
        }
    }

    public function getMinMaxValue($value)
    {
        $numbers = explode(' - ', $value);

        // If custom message is specified for 0 - 0
        if (!isset($numbers[1])) {
            $minValue = 0;
            $maxValue = 0;
        } else {
            $minValue = (int)$numbers[0];
            $maxValue = (int)$numbers[1];
        }

        return ['min' => $minValue, 'max' => $maxValue];
    }

    public function getModelFieldValue(BaseModel $model, $handle, $params)
    {
        $minFee = $model->getAttribute('minfee');
        $maxFee = $model->getAttribute('maxfee');

        if (empty($minFee) OR empty($maxFee)) {
            return false;
        }

        return number_format($minFee).' - '.number_format($maxFee);
    }

    public function getOrderQuery($parts)
    {
        $key = $parts[1].$parts[0];

        $order = $parts[2];

        return "element.$key $order";
    }
}