<?php

namespace Gigtrooper\Services;

use Gigtrooper\Elements\BaseElement;
use Gigtrooper\Models\BaseModel;

class SubField
{
	private $subsHtml = array();

	public function getSubFields(BaseModel $model)
	{
		$id    = $model->id;
		$label = $model->getLabel();

		$queryString = "
		MATCH (model:$label)<-[:PARENT_OF*0..5]-()<-[:SUBFIELD_OF]-(subfields) 
		WHERE model.id = {id} 
		RETURN subfields
			";
		//echo $queryString; exit;
		$results = \Neo4jQuery::getResultSet($queryString, array('id' => $id));

		$nodes = array();

		if ($results->count())
		{
			foreach ($results as $result)
			{
				$nodes[] = $result['subfields'];
			}
		}

		return $nodes;
	}

	public function getSubFieldsHandles(BaseModel $model)
	{
		$nodes = $this->getSubFields($model);

		$handles = array();

		if (!empty($nodes))
		{
			foreach ($nodes as $node)
			{
				$handles[] = $node->handle;
			}
		}

		return $handles;
	}

	/**
	 * @param BaseModel   $model
	 * @param BaseElement $element
	 *
	 * @return $this
	 */
	public function getSubFieldsHtml(BaseModel $model, BaseElement $element)
	{
		$fields = $this->getSubFields($model);

		$fieldTypes = \App::make('fieldTypes');

		$this->subsHtml = array();

		if (!empty($fields))
		{
			foreach ($fields as $field)
			{
				$handle = $field->handle;

				$fieldType = $fieldTypes->getFieldByHandle($handle);

				$fieldClass = \Field::getFieldClass($fieldType);

				$fieldClass->setElement($element);

				$this->subsHtml[$handle] = $fieldClass;
			}
		}

		return $this;
	}

	public function getSubsHtml()
	{
		return $this->subsHtml;
	}

	public function getDisplaySubsHtml()
	{
		$view = '';

		if (!empty($this->subsHtml))
		{
			foreach ($this->subsHtml as $handle => $html)
			{
				$view.= "<div class='form-group field field-$handle'>";
				$view.= $html->getInputHtml($handle);
				$view.= '</div>';
			}
		}

		return $view;
	}

	public function getSearchSubsHtml()
	{
		$view = '';

		if (!empty($this->subsHtml))
		{
			foreach ($this->subsHtml as $handle => $html)
			{
				$view.= "<div class='search-field bottom15'>";
				$view.= $html->getSearchHtml($handle);
				$view.= "</div>";
			}
		}

		return $view;
	}

	public function getValues()
	{
		$results = [];

		if (!empty($this->subsHtml))
		{
			foreach ($this->subsHtml as $handle => $html)
			{
				$results[$handle]['title'] = $html->getTitle();

				$element = $html->getElement();

				if ($element->getModel() != null)
				{
					$results[$handle]['value'] = $element->getModel()->getFieldValue($handle);
				}
			}
		}

		return $results;
	}

	public function getSubFieldValues(BaseModel $model, $elementModel)
	{
		$values = [];

		$subFields = $this->getSubFields($model);

		if (!empty($subFields))
		{
			foreach ($subFields as $node)
			{
				$handle = $node->handle;
				$values[$handle] = $elementModel->getFieldValue($handle);

			}
		}

		return $values;
	}

	public function displaySubsHtml()
	{
		echo $this->getDisplaySubsHtml();
	}
}