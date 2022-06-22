<?php

namespace Gigtrooper\Fields;

use Gigtrooper\Models\GenerateField;
use Gigtrooper\Models\BaseModel;
use Illuminate\Support\Str;

class DropdownField extends BaseOptionsField
{
    public function getName()
    {
        return "Dropdown";
    }

    public function getInputHtml($name)
    {
        $select = [];
        $select[] = new OptionData("Select", null, false);

        $options = $this->getValue();

        $options = array_merge($select, $options);

        return view('fields.dropdown', [
            'name' => $name,
            'title' => $this->getTitle(),
            'options' => $options
        ]);
    }

    public function save($handle, $fieldValue, BaseModel &$fromModel)
    {
        $relationship = (isset($this->settings['relationship'])) ? $this->settings['relationship'] : false;
        $property = $this->getFieldProperty();
        $generate = (!empty($this->settings['generate'])) ? true : false;

        $attributes = [
            'handle' => $handle,
            'propertyKey' => $property,
            'fieldValue' => $fieldValue
        ];

        if (!empty($fieldValue)) {
            // Accept only defined values on fieldTypes. To avoid hack
            if (!empty($this->settings['options'])) {
                if (!$this->isOptionExist($fieldValue, $this->settings['options'])) {
                    return false;
                }
            }

            $model = GenerateField::populateModel($attributes);

            $toModel = \Field::generateField($model, $generate);

            if ($toModel == false) {
                return;
            }

            \Neo4jRelation::initRelation($fromModel, $toModel, $relationship);
            \Neo4jRelation::addOne();
        } else {
            $relationship = $this->getFieldRelationship();

            \Neo4jRelation::initRelation($fromModel, [], $relationship);

            \Neo4jRelation::removeFromRelationships();
        }
    }

    public function getSearchHtml()
    {
        $fieldHandle = $this->settings['handle'];

        if (!empty($this->settings['segmentValue'])) {
            $segment = $this->settings['segmentValue'];

            $segmentValue = \Request::segment($segment);

            $requestValues = [$segmentValue];
        } else {
            $requestValues = $this->getRequestValues();
        }

        $select = [];
        $select[] = new OptionData("Any", '*', false);

        $options = $this->getOptionData($requestValues);
        $options = array_merge($select, $options);

        return view('fields.dropdown', [
            'name' => $fieldHandle,
            'title' => $this->getTitle(),
            'class' => 'full-width',
            'options' => $options,
            'key' => 'filters'
        ]);
    }
}