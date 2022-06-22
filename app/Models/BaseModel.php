<?php

namespace Gigtrooper\Models;

use Gigtrooper\Contracts\Model;
use Gigtrooper\Traits\Convertable;
use ReflectionClass;

abstract class BaseModel extends \pdaleramirez\LaravelNeo4jStarter\Models\BaseModel implements Model
{
    use Convertable;

    protected $values = [];
    protected $fields = [];
    protected $dates = [];
    protected $fieldProperty = 'value';
    protected $fieldTypes = [];
    public $id;

    public function getName()
    {
        return get_class($this);
    }

    public function getAttributes($names = null)
    {
        $values = [];
        if ($names === null) {
            $names = $this->attributes();
        }

        foreach ($names as $name) {
            $values[$name] = $this->$name;
        }

        unset($values['attributes']);

        // merge properties and custom fields
        return array_merge($values, $this->attributes);
    }

    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
        $this->$key = $value;
    }

    public function getAttribute($key)
    {
        return $this->$key;
    }

    public function __get($key)
    {
        return "";
    }

    public function attributes()
    {
        $class = new ReflectionClass($this);
        $names = [];
        foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if (!$property->isStatic()) {
                $names[] = $property->getName();
            }
        }

        return $names;
    }

    public static function populateModel($values)
    {
        $model = new static;
        $model->setAttributes($values);

        return $model;
    }

    public static function populateModels($data, $indexBy = null)
    {
        $models = [];

        if (is_array($data)) {
            foreach ($data as $values) {
                $model = static::populateModel($values);

                if ($indexBy) {
                    $models[$model->$indexBy] = $model;
                } else {
                    $models[] = $model;
                }
            }
        }

        return $models;
    }

    /**
     * Query database for single value
     *
     * @param      $handle
     * @param bool $relationship
     *
     * @return null
     */
    public function getFieldValuesByHandle($handle, $relationship = false)
    {
        $fromLabel = $this->getLabel();

        $fid = $this->id;
        $toLabel = $handle;

        if ($relationship == false) {
            $relationship = $toLabel."_OF";

            $relationship = strtoupper($relationship);
        }

        $results = \Neo4jCriteria::getFieldNodes($fromLabel, $fid, $toLabel, $relationship);

        $values = null;

        if (!empty($results)) {
            $values = $results;
        }

        return $values;
    }

    public function setField($handle, $value)
    {
        $this->fields[$handle] = $value;
    }

    public function getAllFields()
    {
        return $this->fields;
    }

    /**
     * Only return values if criteria option has "WITH" params
     *
     * @param      $handle
     * @param bool $relationship
     *
     * @return mixed|null
     */

    public function isWithFieldExist($handle, $relationship = false)
    {
        $result = false;

        if (isset($this->fields[$handle])) {
            $result = true;
        }

        return $result;
    }

    public function getField($handle, $relationship = false)
    {
        $values = null;

        if (isset($this->fields[$handle])) {
            $values = $this->fields[$handle];
        } else {
            $values = $this->getFieldValuesByHandle($handle, $relationship);
        }

        return $values;
    }

    public function setDate($relationship, $value)
    {
        if (is_array($value)) {
            $value = $value[0];
        }

        $this->dates[$relationship] = $value;
    }

    public function getDate($relationship, $time = true)
    {
        $relationship = strtolower($relationship);

        $key = $relationship."xdatetime";
        if (isset($this->dates[$key])) {
            $value = $this->dates[$key];
        } else {
            $relationship = strtoupper($relationship);

            $dateService = \App::make('dateTimeService');

            $value = $dateService->getDate($this, $relationship);
        }

        return $value;
    }

    public function getAllDates()
    {
        return $this->dates;
    }


    public function getFieldFirst($handle, $relationship = false)
    {
        $values = $this->getField($handle, $relationship);

        if ($values != null && is_array($values)) {
            return $values[0];
        }

        return $values;
    }

    public function getFieldsCollect($handle, $property = 'value', $relationship = false)
    {
        $nodes = $this->getField($handle, $relationship);

        $values = [];

        if (!empty($nodes)) {
            $values = $this->convertRowToArray($nodes, $property);
        }

        return implode(", ", $values);
    }

    public function getFieldNodes($handle, $relationship = false)
    {
        $nodes = $this->getField($handle, $relationship);

        return $nodes;
    }

    public function getFieldsArray($handle, $property = 'value', $relationship = false)
    {
        $nodes = $this->getField($handle, $relationship);

        $values = [];

        if (!empty($nodes)) {
            $values = $this->convertRowToArray($nodes, $property);
        }

        return $values;
    }

    public function getJsonToArray($handle, $property = 'value')
    {
        $array = [];

        $nodes = $this->getField($handle);

        if (!empty($nodes)) {
            foreach ($nodes as $node) {
                $properties = array_values(json_decode($node->$property, true));
                $array = array_merge($array, $properties);
            }
        }

        return $array;
    }

    public function getDateTime($relationship)
    {
        $dates = $this->getDate($relationship);

        if (!empty($dates)) {
            return $dates['time'];
        }

        return [];
    }

    public function forceFill($attributes)
    {
        $this->setAttributes($attributes);

        return $this;
    }

    public function getFieldPropertyKey()
    {
        return $this->fieldProperty;
    }

    public function getFieldProperty()
    {
        $property = $this->fieldProperty;

        return $this->$property;
    }

    public function setFieldTypes($fieldTypes)
    {
        $this->fieldTypes = $fieldTypes;
    }

    public function getFieldValue($handle = '', $setParams = [])
    {
        $defaults = [
            'property' => 'value'
        ];

        $params = array_merge($defaults, $setParams);

        if (isset($this->fieldTypes[$handle])) {
            $fieldSettings = $this->fieldTypes[$handle];

            $fieldClass = \Field::getFieldClass($fieldSettings);

            return $fieldClass->getModelFieldValue($this, $handle, $params);
        } else {
            throw new \Exception('Set Field Types');
        }
    }

    public function getFieldValueFirst($handle = '', $setParams = [])
    {
        $results = $this->getFieldValue($handle, $setParams);

        if (is_array($results) && !empty($results)) {
            return $results[0];
        }

        return $results;
    }

    public static function find($id)
    {
        $find = new static;
        $id = (int) $id;

        $model = \Criteria::findByAttributeQuery('id', $id, $find);

        return $model;
    }

    public static function findByAttribute($key, $value, $label = null)
    {
        $find = new static;

        return \Criteria::findByAttributeQuery($key, $value, $find);
    }
}
