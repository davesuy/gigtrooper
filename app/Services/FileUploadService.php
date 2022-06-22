<?php

namespace Gigtrooper\Services;

use Gigtrooper\Models\User;
use Gigtrooper\Models\Field;
use Gigtrooper\Fields\AssetField;

class FileUploadService
{
    public function arrangeValue($label, $fileuploadId, $handle, $fieldValue, $file)
    {
        $prefix = config('filesystems.prefix');

        $url = "/$prefix/$label/$fileuploadId/$handle/$fieldValue";

        $info = [];

        $width = null;
        $height = null;
        try {
            $image = new \Imagick($file->url);
            $dimension = $image->getImageGeometry();
            $width = $dimension['width'];
            $height = $dimension['height'];
        } catch (\Exception $e) {

        }

        if (!empty($fieldValue)) {
            $info = [
                $fieldValue => [
                    'value' => $fieldValue,
                    'url' => $url,
                    'width' => $width,
                    'height' => $height
                ]
            ];
        }

        return $info;
    }

    public function removeAssetValue($fieldValue, $assetJsonValues)
    {
        $assetValues = json_decode($assetJsonValues, true);

        if (!empty($assetValues) && is_array($assetValues)) {
            unset($assetValues[$fieldValue]);
        }
        //dd($assetValues);
        return (!empty($assetValues)) ? json_encode($assetValues) : false;
    }

    public function handle_file_upload($file)
    {
        if (\Auth::check()) {
            $namespace = '\Gigtrooper\\Models\\'.$file->fileuploadLabel;

            $modelId = (int)$file->fileuploadId;

            $fromModel = $namespace::find($modelId);

            $handle = $file->handle;
            $fieldValue = $file->name;

            $assetField = new AssetField;
            $assetField->setFile($file);
            $assetField->save($handle, $fieldValue, $fromModel);
        }
    }

    public function delete($response, $options)
    {
        $handle = $options['fileuploadHandle'];
        $label = $options['fileuploadLabel'];
        $modelId = $options['fileuploadId'];

        $namespace = '\Gigtrooper\\Models\\'.$label;

        $modelId = (int)$modelId;

        $fromModel = $namespace::find($modelId);

        $relationship = $handle."_OF";
        $relationship = strtoupper($relationship);

        $exist = \Neo4jRelation::getEndModel($fromModel, $relationship);

        if ($exist) {
            $fieldModel = $exist;
        } else {
            $fieldModel = new Field;
            $fieldModel->defineLabel($handle);
        }

        $oldValues = $fromModel->getFieldFirst($handle);

        $assetValues = '';

        if ($oldValues != null) {
            $assetValues = $oldValues->value;
        }

        foreach ($response as $name => $deleted) {
            if ($deleted) {
                $uploadService = \App::make('fileUploadService');

                $assetValues = $uploadService->removeAssetValue($name, $assetValues);

                if ($assetValues === false) {
                    \Neo4jRelation::initRelation($fromModel, $fieldModel);
                    \Neo4jRelation::removeToNode();
                } else {
                    $fieldModel->setAttribute('value', $assetValues);

                    $toModel = $fieldModel->save();

                    \Neo4jRelation::initRelation($fromModel, $toModel);
                    \Neo4jRelation::addOne();
                }
            }
        }
    }
}