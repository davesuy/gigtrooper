<?php

namespace Gigtrooper\Fields;

use Gigtrooper\Contracts\Element;
use Gigtrooper\Models\BaseModel;
use Gigtrooper\Services\CategoryService;

class CategoryModelField extends ModelField
{
    protected $value;
    protected $property = 'title';

    public function getName()
    {
        return "Category Model";
    }

    public function getInputHtml($name)
    {
        $ids = $this->getIds();

        $template = 'fields.categoryDropdown';

        if (isset($this->settings['html']) && $this->settings['html'] == 'checkbox') {
            $template = 'fields.categoryCheckbox';
            unset($ids[0]);
        }

        $title = (isset($this->settings['title'])) ? $this->settings['title'] : '';

        $categoryService = \App::make('categoryService');

        $memberCategoryModel = $this->getInitModel();

        $branch = $categoryService->getBranch($memberCategoryModel);

        $fieldOptions = $this->getFieldOptions();

        $idValue = (!empty($fieldOptions)) ? $fieldOptions[0] : '';

        $default = [
            0 => [
                'id' => 0,
                'title' => 'NONE',
                'slug' => ''
            ]
        ];

        $branch = array_merge($default, $branch);

        $categoryDisplay = $categoryService->getSelectDropdown($branch);

        return view($template, [
            'name' => $name,
            'idValue' => $idValue,
            'categoryDisplay' => $categoryDisplay,
            'title' => $title,
            'options' => $ids,
            'text' => '',
            'modelId' => (!empty($this->element->getModel())) ? $this->element->getModel()->id : null,
            'modelName' => ($memberCategoryModel) ? class_basename($memberCategoryModel) : null,
            'subfield' => (!empty($this->settings['subfield'])) ? $this->settings['subfield'] : null
        ]);
    }

    protected function getModels()
    {
        $modelName = $this->settings['model'];

        $namespace = '\Gigtrooper\\Models\\'.$modelName;

        $initModel = new $namespace;

        $label = $initModel->getLabel();

        // Do this for regular ModelField
        if (!$this->isCategory()) {
            return parent::getModels();
        }

        $arrayIds = [];

        if (isset($this->element) && $this->element->getModel() != null) {
            $arrayIds['catId'] = $this->element->getModel()->id;
            $queryString = "
            MATCH (cat:$label)
            WHERE cat.id = {catId}
            OPTIONAL MATCH (cat)-[:PARENT_OF*]->(child:$label) \n
            WITH cat, COLLECT(DISTINCT(child)) as children
            MATCH (c:$label)
            WHERE c <> cat AND NOT(c IN children)
            RETURN DISTINCT(c)";
        } else {
            $queryString = "
            MATCH (c:$label)
            RETURN c
            ";
        }

        $results = \Neo4jQuery::getResultSet(
            $queryString,
            $arrayIds);

        $models = [];

        if ($results->count()) {
            foreach ($results as $result) {
                $properties = $result['c']->getProperties();

                $models[] = $initModel::populateModel($properties);
            }
        }

        return $models;
    }

    public function getFieldProperty()
    {
        return 'title';
    }

    public function getMatchesTax()
    {
        $handle = $this->settings['handle'];

        $label = $this->getModelLabel();

        $elementNode = "(element)";
        $withRelationship = $this->getFieldRelationship();

        return "($handle:$label)-[:PARENT_OF*0..5]->()-[:$withRelationship]->$elementNode";
    }

    public function getSearchHtml()
    {
        $categoryService = \App::make('categoryService');

        $modelName = $this->settings['model'];
        $url = $this->settings['url'];
        $handle = $this->settings['handle'];

        $namespace = '\Gigtrooper\\Models\\'.$modelName;

        $initModel = new $namespace;

        $categories = $categoryService->getModelsNoChild($initModel);

        $tree = $categoryService->getTree($initModel);

        $baseUrl = \Config::get('app.cp')."/$url/";

        $requestValues = $this->getRequestValues();

        if (!empty($requestValues)) {
            foreach ($requestValues as $requestValue) {
                $label = $initModel->getLabel();
                $whereKey = $this->settings['whereKey'] ?? 'id';
                $children = $categoryService->getChildCategories($label, $requestValue, $whereKey);
                $requestValues = array_merge($children, $requestValues);
            }
        }

        $params = [
            'baseUrl' => $baseUrl,
            'handle' => $handle,
            'requestValues' => $requestValues
        ];

        return view('fields.categorysearch', [
            'categories' => $categories,
            'title' => $this->settings['title'],
            'url' => $url,
            'handle' => $handle,
            'requestValues' => $requestValues,
            'menuTree' => $categoryService->searchMenu($tree, $params)
        ]);
    }

    private function isCategory()
    {
        $result = false;

        $modelName = $this->settings['model'];

        $namespace = '\Gigtrooper\\Models\\'.$modelName;

        $initModel = new $namespace;

        $label = $initModel->getLabel();

        $elementModel = $this->element->initModel();

        $elementModelLabel = $elementModel->getLabel();

        if ($label == $elementModelLabel) {
            $result = true;
        }

        return $result;
    }

    public function setElement(Element $element)
    {
        parent::setElement($element);

        if ($this->isCategory()) {
            $this->settings['relationship'] = 'PARENT_OF';
        }
    }
}