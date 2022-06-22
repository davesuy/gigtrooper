<?php

namespace Gigtrooper\Traits;

trait Convertable
{
    public function convertRowToArray($rows, $property = false)
    {
        if (!empty($rows)) {
            $values = [];
            foreach ($rows as $row) {

                if ($property != false) {
                    $values[] = $row->$property;
                } else {
                    $values[] = $row;
                }
            }

            return $values;
        }

        return $rows;
    }

    /**
     * Return true if not specified and if specified return true if value is "true"
     *
     * @param        $handle
     * @param        $fields
     * @param string $key
     *
     * @return bool
     */
    public function isFieldinDefaultTrue($handle, $fields, $key = 'element')
    {
        $field = $this->getFieldByHandle($handle, $fields);

        if (empty($field)) {
            return true;
        } else {
            if (isset($field[$key]) && $field[$key] == true) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param      $handle
     * @param null $fields
     * @param bool $case Handle case sensitive
     *
     * @return array
     */
    public function getFieldByHandle($handle, $fields = null, $case = true)
    {
        if ($fields == null) {
            $fields = $this->getFieldTypes();
        }

        $value = self::getTheFieldByKey('handle', $handle, $fields, $case);

        if ($value == null) {
            throw new \Exception("Cannot find field by handle $handle.");
        }

        return $value;
    }

    public static function getTheFieldByKey($key, $value, $fields)
    {
        if (!empty($fields)) {
            foreach ($fields as $fieldKey => $field) {
                if ($value == $field[$key]) {
                    return $field;
                }
            }
        }

        return null;
    }

    public function typeCastToInt($value)
    {
        return (int)$value;
    }
}