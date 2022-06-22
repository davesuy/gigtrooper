<?php namespace Gigtrooper\Extensions;

use Gigtrooper\Models\BaseModel;
use Gigtrooper\Models\GenericUser;

class Neo4jAuthProvider extends \pdaleramirez\LaravelNeo4jStarter\Extensions\Neo4jAuthProvider
{
    private function userArray(BaseModel $user)
    {
        $userArray = [];
        $userArray['id'] = $user->id;
        $userArray['email'] = $user->email;
        $userArray['password'] = $user->password;
        $userArray['name'] = $user->name;
        $userArray['remember_token'] = $user->remember_token;

        $roles = $user->getFieldsArray('Role', 'value');

        $userArray['roles'] = $roles;

        $status = $user->getFieldsArray('Status', 'value');

        if (!empty($status)) {
            $userArray['status'] = $status;
        }

        return $userArray;
    }

    public function retrieveByID($identifier)
    {
        $model = $this->createModel();

        $user = $model::find($identifier);
     
        if ($user != null) {
            $userArray = $this->userArray($user);

            return $this->getGenericUser((array)$userArray);
        }
    }

    protected function getGenericUser($user)
    {
        if ($user !== null)
        {
            return new GenericUser($user);
        }

        return $user;
    }

    public function retrieveByCredentials(array $credentials)
    {
        $model = $this->createModel();

        if ($user = $model::findByAttribute("email", $credentials['email'])) {
            $status = $user->getFieldsArray('Status', 'value');

            if (in_array('unverified', $status)) {
                return null;
            }

            return $user;
        }

        return $model;
    }


    public function retrieveByToken($identifier, $token)
    {
        $namespace = $this->createModel();
        $model = new $namespace;

        if ($user = $model::findByAttribute("remember_token", $token)) {
            $userArray = $this->userArray($user);

            return $this->getGenericUser((array)$userArray);
        }
    }
}