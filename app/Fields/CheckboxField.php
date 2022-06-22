<?php

namespace Gigtrooper\Fields;


class CheckboxField extends BaseOptionsField
{

	public function getName()
	{
		return "Checkbox";
	}

	public function getInputHtml($name)
	{
		$options = $this->getValue();

		return view('fields.checkbox',[
			'name'    => $name,
			'title'   => $this->getTitle(),
			'options' => $options
		]);
	}

	public function getSearchHtml()
	{
		$fieldHandle = $this->settings['handle'];

		$requestValues = $this->getRequestValues();

		$options = $this->getOptionData($requestValues);

		return view('fields.search-checkbox',[
			'name'    => $fieldHandle,
			'title'   => $this->getTitle(),
			'options' => $options,
			'all'     => (empty($requestValues))? true : false,
			'key' => 'filters'
		]);
	}
}