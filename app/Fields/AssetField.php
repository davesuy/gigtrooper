<?php

namespace Gigtrooper\Fields;

use Gigtrooper\Models\Asset;
use Gigtrooper\Models\Field;
use Gigtrooper\Models\BaseModel;
use Gigtrooper\Services\FileUploadService;

class AssetField extends BaseField
{
    protected $file;

    public function getName()
    {
        return "Asset";
    }

    public function getInputHtml($handle, $params = [])
    {
        if (!isset($params['model'])) {
            return '';
        }

        $value = $this->getValue();

        return view('fields.asset', [
            'id' => $params['model']->id,
            'title' => $this->getTitle(),
            'label' => $params['model']->getLabel(),
            'handle' => $handle,
            'value' => $value,
            'params' => $params,
            'limit' => (!empty($this->settings['limit'])) ? $this->settings['limit'] : 10
        ]);
    }

    public function setFile($file)
    {
        $this->file = $file;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getProcessPost()
    {
        return false;
    }

    public function save($handle, $fieldValue, BaseModel &$fromModel)
    {
        $label = $handle;
        $fieldModel = new Field;
        $fieldModel->defineLabel($label);

        $oldValues = $fromModel->getFieldFirst($handle);

        $modelLabel = $fromModel->getLabel();
        $fileuploadId = $fromModel->id;

        $service = \App::make('fileUploadService');
        $modelLabel = strtolower($modelLabel);

        $file = $this->file;

        $fieldValue = $service->arrangeValue($modelLabel, $fileuploadId, $handle, $fieldValue, $file);

        if ($oldValues != null && $fieldValue != false) {
            $relationship = $label."_OF";
            $relationship = strtoupper($relationship);
            $exist = \Neo4jRelation::getEndModel($fromModel, $relationship);

            if ($exist) {
                $fieldModel = $exist;
            }

            $assetValues = json_decode($oldValues->value, true);

            $assetValues = array_merge($assetValues, $fieldValue);

            $fieldModel->setAttribute('value', json_encode($assetValues));

            $toModel = $fieldModel->save();
        } else {
            $assetValues = $fieldValue;

            $jsonValues = json_encode($assetValues);

            $fieldModel->setAttribute('value', $jsonValues);

            $toModel = $fieldModel->save();
        }

        \Neo4jRelation::initRelation($fromModel, $toModel);
        \Neo4jRelation::addOne();
    }

    public function getModelFieldValue(BaseModel $model, $handle, $params)
    {
        $values = $model->getJsonToArray($handle);

        $models = [];

        if (!empty($values)) {
            foreach ($values as $value) {
                $asset = new Asset();

                $asset->setAttributes($value);
                $models[] = $asset;
            }
        }

        return $models;
    }
}