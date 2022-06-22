<?php

namespace Gigtrooper\Elements;

use Gigtrooper\Contracts\Element;
use Gigtrooper\Traits\Convertable;

abstract class BaseElement implements Element
{
    use Convertable;

    protected $fieldTypes = [];
    protected $modelId;
    protected $model = null;
    protected $label;

    public function setFieldTypes($fieldTypes)
    {
        $this->fieldTypes = $fieldTypes;
    }

    public function getFieldTypes()
    {
        return $this->fieldTypes;
    }

    public function initModel()
    {
        $name = $this->defineModel();

        $namespace = '\Gigtrooper\\Models\\'.$name;

        $model = new $namespace;

        return $model;
    }

    public function findModel($id)
    {
        $model = $this->initModel();

        $this->model = $model::find($id);

        return $this->model;
    }

    public function setModel($model)
    {
        $this->model = $model;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function defineModel()
    {
        return $this->label;
    }

    public function defineAttributes()
    {
        return $this->initModel()->defineAttributes();
    }

    public function getFieldModelByHandle($handle)
    {
        $field = $this->getFieldByHandle($handle);

        if (!isset($field)) {
            throw new \Exception('Could not find field handle');
        }
        return $field['field'];
    }

    public function getOptionElements($key, $return = 'handle')
    {
        $fields = $this->getFieldTypes();
        $inElements = [];
        if (!empty($fields)) {

            foreach ($fields as $field) {
                if (isset($field[$key]) && $field[$key] == true) {
                    if ($return == 'all') {
                        $inElements[] = $field;
                    } else {
                        $inElements[] = $field[$return];
                    }
                }
            }
        }

        return $inElements;
    }

    public function isFieldinElements($handle)
    {
        $inElements = $this->getOptionElements('element');

        if (in_array($handle, $inElements) || $handle == 'id') {
            return true;
        }

        return false;
    }

    public function deletes(array $ids)
    {
        if (!empty($ids)) {
            $model = $this->initModel();
            $elementLabel = $model->getLabel();

            return \App::make('elementsService')->deleteElementByIds($this, $ids);
        } else {
            return [];
        }
    }
}