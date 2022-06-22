<?php

namespace Gigtrooper\Fields;

use Gigtrooper\Models\GenerateField;
use Gigtrooper\Models\BaseModel;

class RadiobuttonField extends BaseOptionsField
{

	public function getName()
	{
		return "Radiobutton";
	}

	public function getInputHtml($name)
	{
		$options = $this->getValue();

		return view('fields.radiobutton',[
			'name'    => $name,
			'options' => $options,
			'title'   => (!empty($this->settings['title']))? $this->settings['title'] : ucwords($name)
		]);
	}

	public function save($handle, $fieldValue, BaseModel &$fromModel)
	{
		$relationship = (isset($this->settings['relationship'])) ? $this->settings['relationship'] : false;
		$property = $this->getFieldProperty();
		$generate = (!empty($this->settings['generate'])) ? true : false;

		$attributes = array(
			'handle'      => $handle,
			'propertyKey' => $property,
			'fieldValue'  => $fieldValue
		);

		if (!empty($fieldValue))
		{
			// Accept only defined values on fieldTypes. To avoid hack
			if (!empty($this->settings['options']))
			{
				if (!$this->isOptionExist($fieldValue, $this->settings['options']))
				{
					return false;
				}
			}

			$model = GenerateField::populateModel($attributes);

			$toModel = \Field::generateField($model, $generate);

			if ($toModel == false) return;

			\Neo4jRelation::initRelation($fromModel, $toModel, $relationship);
			\Neo4jRelation::addOne();
		}
		else
		{
			$relationship = $this->getFieldRelationship();

			\Neo4jRelation::initRelation($fromModel, array(), $relationship);

			\Neo4jRelation::removeFromRelationships();
		}
	}

	public function getSearchHtml()
	{
		$fieldHandle = $this->settings['handle'];

		$requestValues = $this->getRequestValues();

		$select = array();
		$select[] = new OptionData("Any",  '*', true);

		$options = $this->getOptionData($requestValues);
		$options = array_merge($select, $options);

		return view('fields.radiobutton',[
			'name'    => $fieldHandle,
			'title'   => $this->getTitle(),
			'options' => $options,
			'key' => 'filters'
		]);
	}
}