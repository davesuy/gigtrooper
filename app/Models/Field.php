<?php

namespace Gigtrooper\Models;


class Field extends BaseModel
{

    //public function defineAttributes()
    //{
    //	$defaultAttributes = parent::defineAttributes();
    //
    //	$attributes = array(
    //		'value'
    //	);
    //
    //	return array_merge($defaultAttributes, $attributes);
    //}

    public function findField($id)
    {
        return $this->findFieldByAttribute('id', $id);
    }

    public function findFieldByAttribute($key, $value, $label = null)
    {

        if ($label == null) {
            $label = $this->label;
        }

        $queryString = $this->findByAttributeQuery($key, $label);

        $this->addQueryString($queryString, ['findValue' => $value]);

        return $this->query();
    }

    public function save($transaction = false)
    {
        $model = parent::save();

        $handle = $this->getLabel();

        // Set label to dynamic to return label on object
        $model->defineLabel($handle);

        return $model;
    }
}