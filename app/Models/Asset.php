<?php

namespace Gigtrooper\Models;

class Asset extends BaseModel
{
	protected $fieldProperty = 'value';

  public function getLabel()
  {
      return false;
  }

  public function getThumbUrl()
  {
  	$baseName = baseName($this->url);

  	return dirname($this->url) . '/thumbnail/' . $baseName;
  }
}