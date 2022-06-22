<?php

namespace Gigtrooper\Services\fields;


class Members
{
    public static function getData()
    {
        $fields = [];

        /**
         * Member Categories Tags
         */
        $fields[] = [
            'title' => 'Member Category',
            'handle' => 'memberCategory',
            'field' => 'CategoryModelField',
            'property' => 'title',
            'url' => 'member-categories',
            'model' => 'MemberCategory',
            'whereKey' => 'slug',
            'subfield' => "#subfields-content-memberCategory"
        ];

        $numberOfMembers = [];

        for ($i = 1; $i <= 20; $i++) {
            $numberOfMembers[$i]['label'] = $i;
            $numberOfMembers[$i]['value'] = $i;
        }

        $fields[] = [
            'title' => 'Number of Members',
            'handle' => 'numberOfMembers',
            'generate' => true,
            'field' => 'DropdownField',
            'options' => $numberOfMembers
        ];

        $yearsOfExperience = [];

        for ($i = 1; $i <= 15; $i++) {
            $yearsOfExperience[$i]['label'] = $i;
            $yearsOfExperience[$i]['value'] = $i;
        }

        $yearsOfExperience[16]['label'] = "15+";
        $yearsOfExperience[16]['value'] = "15+";

        $fields[] = [
            'title' => 'Years of Experience',
            'handle' => 'yearsOfExperience',
            'generate' => true,
            'field' => 'DropdownField',
            'options' => $yearsOfExperience
        ];

        $fields[] = [
            'title' => 'Member Category Image',
            'handle' => 'memberCategoryImage',
            'field' => 'AssetField',
            'limit' => 1
        ];

        return $fields;
    }
}