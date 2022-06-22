<?php

namespace Gigtrooper\Services;

use Gigtrooper\Elements\BaseElement;
use Gigtrooper\Events\OnElementDelete;
use Gigtrooper\Traits\IOHelper;

class ElementsService
{
    use IOHelper;

    public function getElement($name)
    {
        $name = $name."Element";
        $allElements = $this->getAllElements(true);

        if (in_array($name, $allElements)) {
            $namespace = "\\Gigtrooper\\Elements\\".$name;

            return new $namespace;
        }

        return null;
    }

    public function getAllElements($title = false)
    {
        $classes = $this->getAllClassesByAppFolder("Elements", "BaseElement", $title);

        return $classes;
    }

    public function getQueryUrl($key = null, $value = '', $baseUrl = '', $queryString = null)
    {
        $query = [];

        if ($queryString == null) {
            $queryString = $_SERVER['QUERY_STRING'];
        }

        $url = '';

        if ($key != null && !empty($value)) {
            parse_str($queryString, $query);

            $query[$key] = $value;
        } elseif ($key != null && empty($value)) {
            parse_str($queryString, $query);

            unset($query[$key]);
        }

        if (!empty($query)) {
            $url = '?'.http_build_query($query);
        }


        return $baseUrl.$url;
    }

    public function getModelsWithFields($models = [], $fieldTypes = [])
    {
        $modelFields = [];
        if (!empty($models)) {
            foreach ($models as $model) {
                $model->setFieldTypes($fieldTypes);
                $modelFields[] = $model;
            }
        }

        return $modelFields;
    }

    public function deleteElementByIds(BaseElement $element, $ids = [])
    {
        $model = $element->initModel();

        $elementLabel = $model->getLabel();

        if ($ids) {
            event(new OnElementDelete($element, $ids));
        }

        $ids = \Neo4jCriteria::deleteNodesByIds($elementLabel, $ids);

        if ($ids) {
            return $ids;
        }

        return false;
    }
}