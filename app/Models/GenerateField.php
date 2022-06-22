<?php

namespace Gigtrooper\Models;


class GenerateField extends BaseModel
{
    public function defineAttributes()
    {
        return [
            'handle',
            'fieldValue',
            'propertyKey'
        ];
    }
}