<?php

namespace Gigtrooper\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Gigtrooper\Services\DateService;
use Illuminate\Notifications\Notifiable;

class User extends BaseModel implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, Notifiable;

    protected $dateService;
    protected $fieldProperty = 'name';
    public $loginMethod;
    public $name;

    public function __construct()
    {
        parent::__construct();
        $this->dateService = new DateService;
    }

    public function defineAttributes()
    {
        return [
            'id',
            'name',
            'email',
            'password',
            'remember_token'
        ];
    }

    public function save($transaction = false)
    {
        $model = parent::save($transaction);

        //$this->dateService->attachNowDate($model);

        return $model;
    }

    public function getProfileUrl()
    {
        $url = null;
        $fieldTypes = \App::make('fieldTypes');
        $fieldTypes = $fieldTypes->getFieldsByHandles(['memberCategory']);
        $this->setFieldTypes($fieldTypes);

        if ($category = $this->getFieldValueFirst('memberCategory')) {
            $slug = $category->slug;
            $url = '/' . $slug . '/' . $this->slug;
        }

        return $url;
    }
}