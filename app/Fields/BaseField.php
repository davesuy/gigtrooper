<?php

namespace Gigtrooper\Fields;

use Gigtrooper\Contracts\Field;
use Gigtrooper\Contracts\Element;
use Gigtrooper\Models\BaseModel;

abstract class BaseField implements Field
{
    private $value;
    protected $settings;
    protected $property = 'value';
    protected $element;

    /**
     * BaseField constructor.
     *
     * @param array $settings
     */
    public function __construct($settings = [])
    {
        $this->settings = $settings;
    }

    public function getTitle()
    {
        return (isset($this->settings['title'])) ? $this->settings['title'] : $this->settings['handle'];
    }

    public function getSettings()
    {
        return $this->settings;
    }

    public function setElement(Element $element)
    {
        $this->element = $element;
    }

    public function getElement()
    {
        return $this->element;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        $value = '';

        if (\Request::old('fields') != null) {
            $oldFields = \Request::old('fields');
            $fieldHandle = $this->settings['handle'];

            if (isset($oldFields[$fieldHandle])) {
                $value = $oldFields[$fieldHandle];
            }
        } elseif ($this->element->getModel() != null) {
            $fieldValues = $this->element->getModel()->getFieldFirst($this->settings['handle']);

            $property = $this->getFieldProperty();

            if (!empty($fieldValues)) {
                $value = $fieldValues->$property;
            }
        }

        return $value;
    }

    public function getFieldProperty()
    {
        $property = (!empty($this->settings['propertyKey'])) ? $this->settings['propertyKey'] : 'value';

        return $property;
    }

    public function addFooterJs($name, $model)
    {
        return '';
    }

    public function getProcessPost()
    {
        return true;
    }

    public function getProperty()
    {
        return $this->property;
    }

    public function getMatchesTax()
    {
        $handle = $this->settings['handle'];

        $elementNode = "(element)";
        $withRelationship = strtoupper($handle)."_OF";

        $match = "($handle:$handle)-[:$withRelationship]->$elementNode";

        return $match;
    }

    public function getReturnTax()
    {
        $handle = $this->settings['handle'];

        $match = "$handle AS $handle";

        return $match;
    }

    public function getWhereCql($value)
    {
        $handle = $this->settings['handle'];
        $property = $this->getFieldProperty();

        $where = "$handle.$property $value";

        return $where;
    }

    public function getOrderTax()
    {
        $handle = $this->settings['handle'];

        $property = $this->getFieldProperty();

        return "$handle.$property AS orderx".$handle;
    }

    public function getOrderQuery($order)
    {
        $handle = $this->settings['handle'];
        return "orderx$handle $order";
    }

    public function prepareWhereValue($handle, $value)
    {
        $whereKey = $handle.'Where';

        return [$whereKey => $value];
    }


    protected function getFieldRelationship()
    {
        if (isset($this->settings['relationship'])) {
            $relationship = $this->settings['relationship'];
        } else {
            // Use handle as relation instead of category model label
            $handleRelationship = $this->settings['handle'].'_OF';

            $relationship = strtoupper($handleRelationship);
        }

        return $relationship;
    }

    public function validate($handle, $fieldValue, BaseModel &$fromModel)
    {
        return true;
    }


    protected function getRequestValues($request = null)
    {
        $requestValues = [];
        $fieldHandle = $this->settings['handle'];

        if ($request == null) {
            $request = \Request::get('f');
        }

        if ($request !== null) {
            $oldFields = $request;

            if (isset($oldFields[$fieldHandle])) {
                $requestValues = $oldFields[$fieldHandle];

                if (is_string($requestValues)) {
                    $requestValues = [$requestValues];
                }
            }
        }

        return $requestValues;
    }

    public function getOperatorValues($value, $valueKey = '', $operator)
    {
        if (is_bool($value)) {
            $value = ($value == true) ? '= true' : '= false';
        } elseif ($value == "*") {
            $value = "IS NOT NULL";
        } elseif (is_array($value)) {
            $value = "IN $valueKey";
        } else {
            $value = "$operator $valueKey";
        }

        return $value;
    }
}