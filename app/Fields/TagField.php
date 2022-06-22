<?php

namespace Gigtrooper\Fields;

use Gigtrooper\Models\GenerateField;
use Gigtrooper\Models\BaseModel;

class TagField extends BaseOptionsField
{

    public function getName()
    {
        return "Tag";
    }

    public function getInputHtml($handle)
    {
        $options = $this->getFieldValues();

        if ($this->element->getModel() != null) {
            $model = $this->element->getModel();
            $id = $model->id;
        } else {
            $model = $this->element->initModel();
            $id = '';
        }

        $label = $model->getLabel();

        return view('fields.taghandler', [
            'handle' => $handle,
            'options' => $options,
            'id' => $id,
            'label' => $label,
            'property' => $this->getFieldProperty()
        ]);
    }

    public function save($handle, $fieldValue, BaseModel &$fromModel)
    {
        $relationship = (isset($this->settings['relationship'])) ? $this->settings['relationship'] : false;
        $property = $this->getFieldProperty();

        $attributes = [
            'handle' => $handle,
            'propertyKey' => $property
        ];

        if (is_array($fieldValue) && !empty($fieldValue)) {
            $toModels = [];
            foreach ($fieldValue as $value) {
                $attributes['fieldValue'] = strtolower($value);

                $model = GenerateField::populateModel($attributes);

                $toModel = \Field::generateField($model, true);

                if ($toModel == false) {
                    continue;
                }

                $toModels[] = $toModel;
            }

            \Neo4jRelation::initRelation($fromModel, $toModels, $relationship);
            \Neo4jRelation::sync();
        } else {
            $relationship = $this->getFieldRelationship();

            \Neo4jRelation::initRelation($fromModel, [], $relationship);

            \Neo4jRelation::removeFromRelationships();
        }
    }

    public function getModelFieldValue(BaseModel $model, $handle, $params)
    {
        $nodes = $model->getField($handle);

        return \Field::getTagLinks($nodes, $params);
    }
}