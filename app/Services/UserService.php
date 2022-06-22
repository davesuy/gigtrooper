<?php

namespace Gigtrooper\Services;

use Gigtrooper\Models\BaseModel;
use Gigtrooper\Models\User;
use Illuminate\Support\Str;

class UserService
{
	public function attachUser($fromModel, $userId, $relationship = false)
	{
		$toModel = User::find($userId);
		if ($toModel != null)
		{
			\Neo4jRelation::initRelation($fromModel, $toModel, $relationship);
			\Neo4jRelation::addOne();
		}
	}

	public function attachCurrentUser($fromModel, $relationship = 'AUTHOR_OF')
	{
		if (\Auth::check())
		{
			$toModel = User::find(\Auth::user()->id);

			if ($toModel != null)
			{
				\Neo4jRelation::initRelation($fromModel, $toModel, $relationship);
				\Neo4jRelation::addOne();
			}
		}
	}

	public function isPostAuthor(BaseModel $post)
	{
		$authorModels = $post->getFieldValue('blogAuthor');

		$authorIds = array();

		if (!empty($authorModels))
		{
			foreach ($authorModels as $authorModel)
			{
				$authorIds[] = $authorModel->id;
			}
		}

		$user = \Auth::user();

		return (in_array($user->id, $authorIds));
	}

	public function isUserRole($role)
	{
		$result = false;

		$user = \Auth::user();

		if ($user != null)
		{
			$roles = $user->roles;

			$result = in_array($role, $roles);
		}

		return $result;
	}

	public function generateUserSlug($name, $id)
	{
		$valueSlug = Str::slug($name);

		$userModel = User::findByAttribute('slug', $valueSlug);

		$slug = $valueSlug;

		if ($userModel)
		{
			$slug = $valueSlug . '-' . $id;
		}

		return $slug;
	}
}