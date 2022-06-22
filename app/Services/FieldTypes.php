<?php

namespace Gigtrooper\Services;

use Gigtrooper\Services\fields\Acts;
use Gigtrooper\Services\fields\Blog;
use Gigtrooper\Services\fields\Common;
use Gigtrooper\Services\fields\Dancers;
use Gigtrooper\Services\fields\Emcee;
use Gigtrooper\Services\fields\Locality;
use Gigtrooper\Services\fields\Members;
use Gigtrooper\Services\fields\Models;
use Gigtrooper\Services\fields\Music;
use Gigtrooper\Services\fields\Service;
use Gigtrooper\Services\fields\Singers;
use Gigtrooper\Services\fields\User;
use Gigtrooper\Traits\Defautltable;

class FieldTypes
{
    use Defautltable;

    private $fieldTypes = [];

    public function getAllFieldTypes()
    {
        $fields = [];

        $fields = array_merge($fields, Acts::getData());
        $fields = array_merge($fields, Blog::getData());
        $fields = array_merge($fields, Common::getData());
        $fields = array_merge($fields, Dancers::getData());
        $fields = array_merge($fields, Emcee::getData());
        $fields = array_merge($fields, Locality::getData());
        $fields = array_merge($fields, Members::getData());
        $fields = array_merge($fields, Models::getData());
        $fields = array_merge($fields, Music::getData());
        $fields = array_merge($fields, Service::getData());
        $fields = array_merge($fields, Singers::getData());
        $fields = array_merge($fields, User::getData());

        return $fields;
    }

    public function setFieldTypes($fieldTypes)
    {
        $this->fieldTypes = $fieldTypes;
    }

    public function indexByHandle()
    {
        $fieldTypes = $this->fieldTypes;

        if (empty($this->fieldTypes)) {
            $fieldTypes = $this->getAllFieldTypes();
        } else {
            $fieldTypes = array_merge($fieldTypes, $this->initDefaultfieldTypes());
        }

        $types = [];

        if (!empty($fieldTypes)) {
            foreach ($fieldTypes as $field) {
                $handle = $field['handle'];

                $types[$handle] = $field;
            }
        }

        return $types;
    }

    public function getFieldsByHandles($handles = [])
    {
        $fields = $this->indexByHandle();

        $fieldTypes = [];

        if (!empty($handles)) {
            foreach ($handles as $handle) {
                if (isset($fields[$handle])) {
                    $fieldTypes[$handle] = $fields[$handle];
                }
            }
        }

        return $fieldTypes;
    }

    public function getFieldByHandle($handle)
    {
        $fieldTypes = $this->indexByHandle();

        if (!isset($fieldTypes[$handle])) {
            throw new \Exception($handle." field not defined.");
        }

        $field = $fieldTypes[$handle];

        return $field;
    }

    public function isOptionExist($options, $value)
    {
        $result = false;

        if (!empty($options)) {
            foreach ($options as $option) {
                if ($option['value'] == $value) {
                    $result = true;
                }
            }
        }

        return $result;
    }

    public function getBlogStatus()
    {
        return [
            'handle' => 'Status',
            'generate' => true,
            'field' => 'DropdownField',
            'options' => [
                [
                    'value' => 'live',
                    'label' => 'Live'
                ],
                [
                    'value' => 'pending',
                    'label' => 'Pending'
                ],
                [
                    'value' => 'disabled',
                    'label' => 'Disabled'
                ],
                [
                    'value' => 'expired',
                    'label' => 'Expired'
                ]
            ]
        ];
    }
}