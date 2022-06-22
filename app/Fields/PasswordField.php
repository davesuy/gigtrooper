<?php

namespace Gigtrooper\Fields;

use Gigtrooper\Models\BaseModel;

class PasswordField extends ElementField
{
	public function getName()
	{
		return "Password";
	}

	public function getInputHtml($name, $params = array())
	{
		$value = $this->getValue();

		$profile = \Request::segment(2);
		$title = $name;
		if ($profile != null AND $profile == 'profile')
		{
			$title = 'new ' . $name;
		}

		return view('fields.password',[
			'title'   => $title,
			'name'    => $name,
			'value'   => $value,
			'params'  => $params
		]);
	}

	public function getValue()
	{
		return '';
	}

	public function getMatchesTax()
	{
		return '';
	}

	public function getReturnTax()
	{
		return '';
	}

	public function getWhereCql($value)
	{
		$handle = $this->settings['handle'];

		return "element.$handle $value";
	}

	public function getOrderTax()
	{
		return '';
	}

	public function getOrderQuery($order)
	{
		$handle = $this->settings['handle'];

		return "element.$handle $order";
	}

	public function save($handle, $fieldValue, BaseModel &$fromModel)
	{
		if (empty($fieldValue)) return;

		$fieldValue = bcrypt($fieldValue);

		$fromModel->setAttribute($handle, $fieldValue);

		// Important to return $fromModel->save() to $fromModel variable to assign back to the loop saving elements.
		$fromModel = $fromModel->save();
	}

	public function getModelFieldValue(BaseModel $model, $handle, $params)
	{
		return $model->getAttribute($handle);
	}
}