<?php

namespace Gigtrooper\Models;

class Category extends BaseModel
{
	protected $fieldProperty = 'title';

  public function getLabel()
  {
      return "Category";
  }
}