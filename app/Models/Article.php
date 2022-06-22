<?php

namespace Gigtrooper\Models;

use Gigtrooper\Services\DateService;
use Gigtrooper\Services\UserService;

class Article extends BaseModel
{
	protected $dateService;

	public function __construct()
	{
		parent::__construct();
		$this->dateService = new DateService;
	}

	public function defineAttributes()
	{
		return array(
			'id',
			'title'
		);
	}

  public function getLabel()
  {
      return "ARTICLE";
  }

	public function save($transaction = false)
	{
		$model = parent::save($transaction);

		$this->dateService->attachNowDate($model);

		$userService = new UserService;
		$userService->attachCurrentUser($model);

		return $model;
	}
}