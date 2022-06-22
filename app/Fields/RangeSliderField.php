<?php

namespace Gigtrooper\Fields;

use Gigtrooper\Models\BaseModel;

class RangeSliderField extends ElementField
{
	private $value = null;

	public function getName()
	{
		return "RangeSlider";
	}

	public function getInputHtml($handle, $params = array())
	{
		$value = $this->getValue();

		return view('fields.rangeslider',[
			'handle'  => $handle,
			'value'   => $value,
			'title'   => $this->getTitle(),
			'params'  => $params,
		  'step'    => (!empty($this->settings['step']))? $this->settings['step'] : 0,
			'message' => (!empty($this->settings['message']))? $this->settings['message'] : '0 - 0'
		]);
	}

	public function getValue()
	{
		try
		{
			$handle = $this->settings['handle'];

			$minValue = 0;
			$maxValue = 0;

			if ($this->element->getModel() != null)
			{
				$min = $this->element->getModel()->getAttribute('min' . $handle);
				$max = $this->element->getModel()->getAttribute('max' . $handle);

				$minValue = (!empty($min)) ? $min : 0;
				$maxValue = (!empty($max)) ? $max : 0;
			}

			return array('min' => $minValue, 'max' => $maxValue);
		}
		catch (\Exception $e)
		{
		}
	}

	public function save($handle, $fieldValue, BaseModel &$fromModel)
	{
		if (!empty($this->settings['disabled'])) return;

		$value = $this->getMinMaxValue($fieldValue);

		$fromModel->setAttribute('min' . $handle, $value['min']);
		$fromModel->setAttribute('max' . $handle, $value['max']);

		// Important to return $fromModel->save() to $fromModel variable to assign back to the loop saving elements.
		$fromModel = $fromModel->save();
	}

	public function getSearchHtml()
	{
		$handle = $this->settings['handle'];
		$value = array('min' => 0, 'max' => 0);
		if(\Request::get('f') !== null)
		{
			$filterFields = \Request::get('f');

			if (isset($filterFields[$handle]))
			{
				$value = $filterFields[$handle];
				$value = $this->getMinMaxValue($value);
			}
		}

		return view('fields.rangeslider',[
			'handle' => $handle,
			'key'    => 'filters',
			'value'  => $value,
			'title'  => '',
			'settings'   => $this->settings,
			'step'    => (!empty($this->settings['step']))? $this->settings['step'] : 0,
		  'message' => '0 - 0'
		]);
	}

	public function prepareWhereValue($handle, $value)
	{
		$value = $this->getMinMaxValue($value);

		$rangeWheres = array(
			"min$handle" => (int) $value['min'],
			"max$handle" => (int) $value['max']
		);

		return $rangeWheres;
	}

	public function getWhereCql($value)
	{
		$handle = $this->settings['handle'];

		return "(element.min$handle >= {min$handle} AND element.min$handle <= {max$handle}) 
		AND (element.max$handle >= {min$handle} AND element.max$handle <= {max$handle})";
	}

	public function getMinMaxValue($value)
	{
		$numbers = explode(' - ', $value);

		// If custom message is specified for 0 - 0
		if (!isset($numbers[1]))
		{
			$minValue = 0;
			$maxValue = 0;
		}
		else
		{
			$minValue = (int) $numbers[0];
			$maxValue = (int) $numbers[1];
		}

		return array('min' => $minValue, 'max' => $maxValue);
	}

	public function getModelFieldValue(BaseModel $model, $handle, $params)
	{
		$minFee = $model->getAttribute('minfee');
		$maxFee = $model->getAttribute('maxfee');

		if (empty($minFee) OR empty($maxFee)) return false;

		return number_format($minFee) . ' - ' . number_format($maxFee);
	}

	public function getOrderQuery($parts)
	{
		$key = $parts[1] . $parts[0];

		$order = $parts[2];

		return "element.$key $order";
	}
}