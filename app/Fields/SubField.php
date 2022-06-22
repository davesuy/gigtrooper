<?php

namespace Gigtrooper\Fields;

class SubField extends BaseOptionsField
{

	public function getName()
	{
		return "SubField";
	}

	public function getFieldProperty()
	{
		return 'handle';
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

	public function getFieldOptions()
	{
		$subFieldService = \App::make('subFieldService');

		$handles = array();

		if ($this->element->getModel() != null)
		{
			$element = $this->element;

			$model   = $this->element->getModel();

			$subFields = $subFieldService->getSubFields($model, $element);

			if (!empty($subFields))
			{
				foreach ($subFields as $subField)
				{
					$handles[] = $subField->handle;
				}
			}
		}

		return $handles;
	}

	protected function getOptionData($optionValues)
	{
		$options = array();
		$fieldTypesService = \App::make('fieldTypes');
		$fieldTypes = $fieldTypesService->indexByHandle();

		if (isset($fieldTypes))
		{
			foreach($fieldTypes as $fieldType)
			{
				$selected = in_array($fieldType['handle'], $optionValues);

				$val = new OptionData($fieldType['handle'], $fieldType['handle'], $selected);
				$options[] = $val;
			}
		}

		return $options;
	}
}