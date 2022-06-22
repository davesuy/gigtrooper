<?php

namespace Gigtrooper\Fields;

use Gigtrooper\Services\FieldTypes;
use Gigtrooper\Traits\Convertable;
use Gigtrooper\Models\BaseModel;

class ModelField extends BaseOptionsField
{
    use Convertable;

    protected $value;

    public function getName()
    {
        return "Model";
    }

    public function getInputHtml($name)
    {
        $ids = $this->getIds();

        $template = 'fields.modelDropdown';

        if (isset($this->settings['html']) && $this->settings['html'] == 'checkbox') {
            $template = 'fields.checkbox';
            unset($ids[0]);
        }

        $title = (isset($this->settings['title'])) ? $this->settings['title'] : '';

        $initModel = $this->getInitModel();

        return view($template, [
            'name' => $name,
            'title' => $title,
            'options' => $ids,
            'modelId' => (!empty($this->element->getModel())) ? $this->element->getModel()->id : null,
            'modelName' => ($initModel) ? class_basename($initModel) : null,
            'subfield' => (!empty($this->settings['subfield'])) ? $this->settings['subfield'] : null
        ]);
    }

    protected function getFieldValues()
    {
        $relationship = $this->getFieldRelationship();

        $toLabel = $this->getInitModel()->getLabel();

        $values = $this->element->getModel()->getFieldsArray($toLabel, 'id', $relationship);

        return $values;
    }

    public function getInitModel()
    {
        $modelName = $this->settings['model'];

        $namespace = '\Gigtrooper\\Models\\'.$modelName;

        $initModel = new $namespace;

        return $initModel;
    }

    protected function getIds()
    {
        $optionValues = $this->getFieldOptions();

        $select = new OptionData("Select", 0, false);

        $options = $this->getOptionData($optionValues);

        array_unshift($options, $select);

        return $options;
    }

    protected function getModels()
    {
        $modelName = $this->settings['model'];

        $namespace = '\Gigtrooper\\Models\\'.$modelName;

        $initModel = new $namespace;

        $fieldKey = $initModel->getFieldPropertyKey();

        if (!empty($this->settings['filter'])) {
            $models = $this->filter($initModel, $fieldKey);
        } else {
            $service = \App::make('fieldTypes');

            $fieldTypes = $service->getFieldsByHandles([$fieldKey]);

            \Criteria::setOptions($initModel, ['order' => [$fieldKey]], $fieldTypes);

            $models = \Criteria::find()->all();
        }

        return $models;
    }

    protected function filter($initModel, $fieldKey)
    {
        $filter = $this->settings['filter'];

        $handle = $filter['handle'];
        $options = [];
        $options['fields'][0]['handles'][0]['handle'] = $handle;
        $options['fields'][0]['handles'][0]['value'] = $filter['value'];

        $options['order'][] = $fieldKey;

        $service = \App::make('fieldTypes');
        $fieldTypes = $service->getFieldsByHandles([$handle, $fieldKey]);

        \Criteria::setOptions($initModel, $options, $fieldTypes);

        return \Criteria::find()->all();
    }

    public function validate($handle, $fieldValue, BaseModel &$fromModel)
    {
        $modelName = $this->settings['model'];

        /**
         * @var $namespace \pdaleramirez\LaravelNeo4jStarter\Models\BaseModel
         */
        $namespace = '\Gigtrooper\\Models\\'.$modelName;

        $values = $fieldValue;

        if (!is_array($fieldValue) && !empty($fieldValue)) {
            $values = [$fieldValue];
        }

        if (!empty($values)) {
            foreach ($values as $value) {
                $value = (int)$value;

                $valueModel = $namespace::find($value);

                if (!$valueModel) {
                    return false;
                }
            }
        }

        return true;
    }

    public function save($handle, $fieldValue, BaseModel &$fromModel)
    {
        $modelName = $this->settings['model'];

        $namespace = '\Gigtrooper\\Models\\'.$modelName;

        $values = $fieldValue;

        $relationship = $this->getFieldRelationship();

        if (!is_array($fieldValue) && !empty($fieldValue)) {
            $values = [$fieldValue];
        }

        if (!empty($values)) {
            $toModels = [];

            foreach ($values as $value) {
                $value = (int)$value;

                $valueModel = $namespace::find($value);

                if (!$valueModel) {
                    throw new \Exception("Invalid value $value give on $handle fieldType should be model id.");
                }

                $toModels[] = $valueModel;
            }

            \Neo4jRelation::initRelation($fromModel, $toModels, $relationship);

            \Neo4jRelation::sync();
        } else {
            \Neo4jRelation::initRelation($fromModel, [], $relationship);

            \Neo4jRelation::removeFromRelationships();
        }
    }

    protected function getOptionData($optionValues)
    {
        $models = $this->getModels();

        $options = [];

        if (!empty($models)) {
            foreach ($models as $model) {
                $selected = false;
                if (!empty($optionValues) && in_array($model->id, $optionValues)) {
                    $selected = true;
                }

                $val = new OptionData(strip_tags($model->getFieldProperty()), $model->id, $selected);

                $options[] = $val;
            }
        }

        return $options;
    }

    public function getMatchesTax()
    {
        $handle = $this->settings['handle'];

        $label = $this->getModelLabel();

        $elementNode = "(element)";
        $withRelationship = $this->getFieldRelationship();

        return "($handle:$label)-[:$withRelationship]->$elementNode";
    }

    protected function getModelLabel()
    {
        $modelName = $this->settings['model'];
        $namespace = '\Gigtrooper\\Models\\'.$modelName;

        $model = new $namespace;
        $label = $model->getLabel();

        return $label;
    }

    public function prepareWhereValue($handle, $value)
    {
        $whereKey = $handle.'Where';

        if ((!empty($this->settings['whereKey'])) && !is_int($value)) {
            $castedValue = $value;
        } else {
            $castedValue = array_map([$this, "typeCastToInt"], $value);
        }

        return [$whereKey => $castedValue];
    }

    public function getWhereCql($value)
    {
        $handle = $this->settings['handle'];

        $whereKey = (!empty($this->settings['whereKey'])) ? $this->settings['whereKey'] : 'id';

        return "$handle.$whereKey $value";
    }

    public function getModelFieldValue(BaseModel $model, $handle, $params)
    {
        $modelName = $this->settings['model'];
        $namespace = '\Gigtrooper\\Models\\'.$modelName;

        $settingModel = new $namespace;
        $label = $settingModel->getLabel();

        $relationship = $this->getFieldRelationship();

        $nodes = $model->getFieldNodes($label, $relationship);

        $result = [];

        if (!empty($nodes)) {
            foreach ($nodes as $node) {
                $properties = $node->getProperties();
                unset($properties['password']);
                $result[] = $namespace::populateModel($properties);
            }
        }

        return $result;
    }

    public function getSearchHtml()
    {
        $fieldHandle = $this->settings['handle'];

        $requestValues = $this->getRequestValues();

        $select = [];
        $select[] = new OptionData("All", '*', false);

        $options = $this->getOptionData($requestValues);
        $options = array_merge($select, $options);

        $template = 'fields.dropdown';

        if (isset($this->settings['html']) && $this->settings['html'] == 'checkbox') {
            $template = 'fields.checkbox';
        }

        return view($template, [
            'name' => $fieldHandle,
            'title' => '',
            'options' => $options,
            'key' => 'filters'
        ]);
    }
}