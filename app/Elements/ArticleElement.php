<?php

namespace Gigtrooper\Elements;


class ArticleElement extends BaseElement
{

	public function getName()
	{
		return "Articles";
	}

	public function defineModel()
	{
		return "Article";
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
			'section'  => array('default'),
			'category' => array('all'),
			'handle'   => 'body',
			'field'    => 'RichTextField',
			'element'  => true
		);

		$fields[] = array(
			'title'    => 'Category',
			'handle'   => 'category',
			'field'    => 'ModelField',
			'model'    => 'Category',
			'multiple' => false
		);

		$fields[] = array(
			'handle'   => 'Tag',
			'field'    => 'TagField'
		);

		$fields[] = array(
			'title'    => 'Author',
			'handle'   => 'articleAuthor',
			'field'    => 'ModelField',
			'model'    => 'User',
			'relationship' => 'AUTHOR_OF',
			'multiple' => false
		);

		return $fields;
	}
}