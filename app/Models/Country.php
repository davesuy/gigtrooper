<?php

namespace Gigtrooper\Models;

class Country extends BaseModel
{
	protected $fieldProperty = 'title';

  public function getLabel()
  {
      return "Country";
  }
}