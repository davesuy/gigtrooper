<?php

namespace Gigtrooper\Models;

use Gigtrooper\Services\UserService;

class Tag extends BaseModel
{
	protected $fieldProperty = 'value';

	public function defineAttributes()
	{
		return array(
			'id',
			'title'
		);
	}

  public function getLabel()
  {
      return "Tag";
  }
}