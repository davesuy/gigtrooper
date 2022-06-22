<?php

namespace Gigtrooper\Models;


class GenericUser extends \Illuminate\Auth\GenericUser
{
    public function getModel()
    {
        return User::populateModel($this->attributes);
    }
}