<?php

namespace Gigtrooper\Traits;

use Gigtrooper\Models\Country;
use Gigtrooper\Models\MemberCategory;

trait SubField
{
    public function getCategorySubFieldHandles($request)
    {
        $subFieldHandles = [];

        $fields = $request->input('fields');

        // This will ensure that only declared handles and the sub fields gets updated and NOT any fields such as Role
        if (!empty($fields['memberCategory'])) {
            $memberCategoryId = $fields['memberCategory'];

            $memberCategoryModel = MemberCategory::find($memberCategoryId);

            $subFieldService = \App::make('subFieldService');

            $subFieldHandles = $subFieldService->getSubFieldsHandles($memberCategoryModel);
        }

        $subFieldHandlesCountry = [];

        if (!empty($fields['Country'])) {
            $memberCategoryId = $fields['Country'];

            $memberCategoryModel = Country::find($memberCategoryId);

            $subFieldService = \App::make('subFieldService');

            $subFieldHandlesCountry = $subFieldService->getSubFieldsHandles($memberCategoryModel);
        }

        return array_merge($subFieldHandles, $subFieldHandlesCountry);
    }
}