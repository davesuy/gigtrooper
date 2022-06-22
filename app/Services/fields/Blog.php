<?php

namespace Gigtrooper\Services\fields;


class Blog
{
    public static function getData()
    {
        $fields = [];


        $fields[] = [
            'title' => 'Category',
            'handle' => 'Category',
            'field' => 'CategoryModelField',
            'property' => 'title',
            'model' => 'Category'
        ];

        $fields[] = [
            'title' => 'Image Gallery',
            'handle' => 'imageGallery',
            'field' => 'AssetField',
            'limit' => 8
        ];

        $fields[] = [
            'handle' => 'imageThumbnail',
            'title' => 'Image Thumbnail',
            'field' => 'PlaintextField'
        ];

        $fields[] = [
            'handle' => 'popularPost',
            'field' => 'NumberField',
            'defaultValue' => 0
        ];

        $fields[] = [
            'handle' => 'Image',
            'field' => 'AssetField',
            'limit' => 20
        ];

        $fields[] = [
            'handle' => 'DatePublished',
            'title' => 'Date Published',
            'field' => 'DateField',
            'day' => true
        ];

        $fields[] = [
            'handle' => 'DateTimePublished',
            'title' => 'Date Time Published',
            'field' => 'DateTimeField',
            'day' => true
        ];

        $fields[] = [
            'handle' => 'DateExpiry',
            'title' => 'Date Expiry',
            'field' => 'DateTimeField',
            'day' => true
        ];

        $fields[] = [
            'handle' => 'Tag',
            'field' => 'TagField'
        ];


        return $fields;
    }
}