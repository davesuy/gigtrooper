<?php

namespace Gigtrooper\Models;

class MemberCategory extends BaseModel
{
    protected $fieldProperty = 'title';

    public function getLabel()
    {
        return "MemberCategory";
    }

    public function getUrl()
    {
        $countryCode = \App::make('countryService')->getSessionCountry();

        return "/search/members/$countryCode/" . $this->slug;
    }
}