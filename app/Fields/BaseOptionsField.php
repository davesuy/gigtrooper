<?php

namespace Gigtrooper\Fields;

use Gigtrooper\Models\BaseModel;
use Gigtrooper\Models\GenerateField;

abstract class BaseOptionsField extends BaseField
{
    private $options;

    public function getOptions()
    {
        return $this->options;
    }

    public function setOptions($options)
    {
        $this->options = $options;
    }

    protected function getFieldValues()
    {
        $values = [];

        $property = $this->getFieldProperty();

        if ($this->element->getModel() != null) {
            $values = $this->element->getModel()->getFieldsArray($this->settings['handle'], $property);
        }

        return $values;
    }

    public function getFieldOptions()
    {
        $values = [];

        $request = \Request::old('fields');

        $requestValues = $this->getRequestValues($request);

        if (!empty($requestValues)) {
            $values = $requestValues;
        } elseif ($this->element->getModel() != null) {
            $values = $this->getFieldValues();
        } else {
            if (isset($this->settings['default'])) {
                $values = $this->settings['default'];
            }
        }

        return $values;
    }

    public function getValue()
    {
        $optionValues = $this->getFieldOptions();

        $options = $this->getOptionData($optionValues);

        return $options;
    }

    protected function getOptionData($optionValues)
    {
        $options = [];

        if (isset($this->settings['options'])) {
            if (!isset($this->settings['sortOptions'])
                OR (isset($this->settings['sortOptions']) AND $this->settings['sortOptions'] == true)) {
                sort($this->settings['options']);
            }

            foreach ($this->settings['options'] as $option) {
                $selected = in_array($option['value'], $optionValues);

                $val = new OptionData($option['label'], $option['value'], $selected);
                $options[] = $val;
            }
        }

        return $options;
    }

    public function save($handle, $fieldValue, BaseModel &$fromModel)
    {
        $relationship = (isset($this->settings['relationship'])) ? $this->settings['relationship'] : false;
        $property = $this->getFieldProperty();

        $generate = (!empty($this->settings['generate'])) ? true : false;

        $attributes = [
            'handle' => $handle,
            'propertyKey' => $property
        ];

        if (is_array($fieldValue) && !empty($fieldValue)) {
            $toModels = [];
            foreach ($fieldValue as $value) {
                $attributes['fieldValue'] = $value;

                // Accept only defined values on fieldTypes. To avoid hack
                if (!empty($this->settings['options'])) {
                    if (!$this->isOptionExist($value, $this->settings['options'])) {
                        continue;
                    }
                }

                $model = GenerateField::populateModel($attributes);

                $toModel = \Field::generateField($model, $generate);

                if ($toModel == false) {
                    continue;
                }

                $toModels[] = $toModel;
            }

            if (empty($toModels)) {
                return;
            }

            \Neo4jRelation::initRelation($fromModel, $toModels, $relationship);
            \Neo4jRelation::sync();
        } else {
            $relationship = $this->getFieldRelationship();

            \Neo4jRelation::initRelation($fromModel, [], $relationship);

            \Neo4jRelation::removeFromRelationships();
        }
    }

    public function getSearchHtml()
    {
        $fieldHandle = $this->settings['handle'];

        $requestValues = $this->getRequestValues();

        $select = [];

        $all = (in_array('*', $requestValues)) ? true : false;
        $select[] = new OptionData("All", '*', $all);

        $options = $this->getOptionData($requestValues);

        $options = array_merge($select, $options);

        return view('fields.search-checkbox', [
            'name' => $fieldHandle,
            'title' => $this->getTitle(),
            'options' => $options,
            'key' => 'filters'
        ]);
    }

    public function getReturnTax()
    {
        $handle = $this->settings['handle'];

        return "COLLECT(DISTINCT($handle)) AS $handle";
    }

    public function getModelFieldValue(BaseModel $model, $handle, $params)
    {
        $property = $params['property'];

        return $model->getFieldsCollect($handle, $property);
    }

    protected function isOptionExist($value, $options)
    {
        $fieldTypes = \App::make('fieldTypes');

        return $fieldTypes->isOptionExist($options, $value);
    }
}