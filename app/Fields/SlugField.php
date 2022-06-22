<?php

namespace Gigtrooper\Fields;

use Gigtrooper\Models\BaseModel;
use Illuminate\Support\Str;

class SlugField extends PlaintextField
{
	public function getName()
	{
		return "Slug";
	}

	public function getInputHtml($name, $params = array())
	{
		$value = $this->getValue();

		return view('fields.slug',[
			'name'    => $name,
			'value'   => $value,
			'params'  => $params
		]);
	}

	public function save($handle, $fieldValue, BaseModel &$fromModel)
	{
		$fieldValue = Str::slug($fieldValue);

		$fromModel->setAttribute($handle, $fieldValue);

		// Important to return $fromModel->save() to $fromModel variable to assign back to the loop saving elements.
		$fromModel = $fromModel->save();
	}
}