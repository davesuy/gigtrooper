<?php

namespace Gigtrooper\Services;

use Gigtrooper\Elements\BaseElement;
use Gigtrooper\Models\BaseModel;
use Gigtrooper\Models\Field;
use Gigtrooper\Models\GenerateField;
use Illuminate\Http\Request;

class FieldService
{
    /**
     * @var BaseElement
     */
    protected $element;
    protected $category;
    protected $section;
    private $fields;
    private $model = null;

    public function __construct()
    {
        $this->model = null;
        $this->element = null;
        $this->fields = null;
    }

    public function setElement(BaseElement $element)
    {
        $this->element = $element;
    }

    public function setSection($section)
    {
        $this->section = $section;
    }

    public function setCategory($category)
    {
        $this->category = $category;
    }

    public function showInputHtml()
    {
        $fields = $this->element->getFieldTypes();

        if (!empty($fields)) {
            foreach ($fields as $field) {
                $fieldClass = \Field::getFieldClass($field);
                $fieldClass->setElement($this->element);

                $this->getFieldInputHtml($field, $fieldClass);
            }
        }
    }

    public function getInputHtmlByHandle($handle)
    {
        $fields = $this->element->getFieldTypes();

        if (!isset($fields[$handle])) {
            throw new \Exception('Could not find field by handle');
        }

        $field = $fields[$handle];

        $fieldClass = \Field::getFieldClass($field);
        $fieldClass->setElement($this->element);

        $this->getFieldInputHtml($field, $fieldClass);
    }

    public function getFieldInputHtml($field, $fieldClass)
    {
        $params = (isset($field['params'])) ? $field['params'] : [];
        $params['model'] = $this->element->getModel();
        $html = $fieldClass->getInputHtml($field['handle'], $params);

        if (!empty($html)) {
            echo "<div class='form-group field field-".$field['handle']."'>";
            echo $html;
            echo '<div class="help-block with-errors"></div>';
            echo "</div>";
        }
    }

    public function isFieldParams($handle, $key)
    {
        $fields = $this->element->getOptionElements('params', 'all');

        if (!empty($fields)) {
            foreach ($fields as $fieldValues) {
                if ($handle == $fieldValues['handle']) {
                    $paramKeys = array_keys($fieldValues['params']);

                    if (in_array($key, $paramKeys)) {
                        return true;
                    }
                }
            }

            return false;
        }
    }

    public function getFieldRules($fields)
    {
        $this->fields = $fields;

        $rules = [];
        if (!empty($fields)) {
            foreach ($fields as $handle => $field) {
                $fieldTypes = $this->element->getFieldByHandle($handle);
                if (isset($fieldTypes['rules'])) {
                    $key = 'fields.'.$handle.'';
                    $rules[$key] = $fieldTypes['rules'];
                }
            }
        }

        return $rules;
    }

    public function getFriendlyNames($fields = null)
    {
        if ($fields == null) {
            $fields = $this->fields;
        }

        $names = [];

        if (!empty($fields)) {
            foreach ($fields as $handle => $field) {
                $fieldName = 'fields.'.$handle;
                $fieldType = $this->element->getFieldByHandle($handle);

                if (isset($fieldType['title'])) {
                    $names[$fieldName] = $fieldType['title'];
                } else {
                    $names[$fieldName] = ucwords($handle);
                }
            }
        }

        return $names;
    }

    /**
     * @param Request $request
     * @param string  $message
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processFields(Request $request, $message = '')
    {
        $fields = $request->input('fields');

        $rules = $this->getFieldRules($fields);

        $friendlyNames = $this->getFriendlyNames();

        $messages = [];
        if (!empty($rules)) {
            $validator = \Validator::make($request->all(), $rules);

            $validator->setAttributeNames($friendlyNames);

            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $this->validateFields($fields);

        $result = $this->saveElementFields($fields);

        if (!$result) {
            return redirect()
                ->back()
                ->withErrors(['msg' => 'Did not save the element.']);
        }

        $messages[] = (!empty($message)) ? $message : 'Element has been added.';
        return redirect()
            ->back()
            ->with('messages', $messages);
    }

    public function setModel($model)
    {
        $this->model = $model;
    }

    public function getModel()
    {
        if ($this->model != null) {
            $fromModel = $this->model;
        } else {
            if ($this->element->getModel() != null) {
                $fromModel = $this->element->getModel();
            } else {
                $fromModel = $this->element->initModel();
            }
        }

        return $fromModel;
    }

    public function validateFields($fields)
    {
        $fromModel = $this->getModel();

        if ($fromModel == null) {
            return false;
        }

        $fieldTypes = $this->element->getFieldTypes();

        if (!empty($fieldTypes)) {
            foreach ($fieldTypes as $fieldType) {
                $handle = $fieldType['handle'];

                if (isset($fields[$handle])) {
                    $fieldValue = $fields[$handle];

                    $fieldModel = \Field::getFieldClass($fieldType);

                    $fieldModel->setElement($this->element);

                    $success = $fieldModel->validate($handle, $fieldValue, $fromModel);

                    if (!$success) {
                        throw new \Exception("The handle $handle does not validate with it's values");
                    }
                }
            }
        }

        return true;
    }

    public function saveElementFields($fields)
    {
        $fromModel = $this->getModel();

        if ($fromModel == null) {
            return false;
        }

        $fieldTypes = $this->element->getFieldTypes();

        if (!empty($fieldTypes)) {
            foreach ($fieldTypes as $fieldType) {
                $handle = $fieldType['handle'];

                $fieldModel = \Field::getFieldClass($fieldType);

                if ($fieldModel->getProcessPost() === false) {
                    continue;
                }
                $fieldValue = '';
                
                if (isset($fields[$handle])) {
                    $fieldValue = $fields[$handle];
                }

                $fieldModel->setElement($this->element);

                $fieldModel->save($handle, $fieldValue, $fromModel);
            }
        }

        return $fromModel;
    }

    public function generateField(GenerateField $model, $new = false)
    {
        $fieldModel = new Field;
        $fieldModel->defineLabel($model->handle);
        $exist = $fieldModel->findFieldByAttribute($model->propertyKey, $model->fieldValue);
        if (!$exist) {
            if ($new == false) {
                return false;
            }
            if ($new == true) {
                $property = $model->propertyKey;
                $fieldModel->setAttribute($property, $model->fieldValue);

                $toModel = $fieldModel->save();
            }
        } else {
            $toModel = $exist;
        }

        return $toModel;
    }

    public function addFooterJsScripts()
    {
        $fields = $this->element->getFieldTypes();

        $scripts = [];

        if (!empty($fields)) {
            try {
                foreach ($fields as $field) {
                    $fieldClass = \Field::getFieldClass($field);
                    $fieldClass->setElement($this->element);

                    $model = $this->getModel();

                    if (!empty($fieldClass->addFooterJs($field['handle'], $model))) {
                        $scripts[] = $fieldClass->addFooterJs($field['handle'], $model);
                    }
                }
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }

        return $scripts;
    }

    public function getFieldClass($settings)
    {
        if (!isset($settings['field'])) {
            throw new \Exception('Missing field '.$settings['handle']);
        }

        $namespace = '\Gigtrooper\\Fields\\'.$settings['field'];
        $fieldClass = new $namespace($settings);

        return $fieldClass;
    }

    public function getElement()
    {
        return $this->element;
    }

    public function getTagLinks($nodes, $params = [])
    {
        $response = '';

        $baseUrl = (!empty($params['baseUrl'])) ? $params['baseUrl'] : '/blog/tags/';

        if (!empty($nodes)) {
            $tags = [];

            foreach ($nodes as $node) {
                $slug = strtolower($node->value);

                $tags[] = "<a href='".$baseUrl.$slug."'>".$node->value."</a>";
            }

            $response = implode(',', $tags);
        }

        return $response;
    }
}