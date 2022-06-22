<?php

namespace Gigtrooper\Elements;


class CategoryElement extends BaseElement
{

	public function getName()
	{
		return "Categories";
	}

	public function defineModel()
	{
		return "Category";
	}

	public function fieldTypes()
	{
		$fields = array();

		$fields[] = array(
			'section'  => array('default'),
			'category' => array('all'),
			'handle'   => 'title',
			'field'    => 'PlaintextField',
			'element'  => true
		);

		$fields[] = array(
			'title'    => 'Category Model',
			'handle'   => 'categoryModel',
			'field'    => 'CategoryModelField',
			'model'    => 'CATEGORY',
			'multiple' => false,
			'relationship' => 'PARENT_OF'
		);

		return $fields;
	}
}