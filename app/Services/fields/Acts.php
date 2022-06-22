<?php

namespace Gigtrooper\Services\fields;


class Acts
{
	public static function getData()
	{
		$fields = [];

		$fields[] = array(
			'title'   => 'Magician Style',
			'handle'   => 'magicianStyle',
			'generate' => true,
			'field'    => 'CheckboxField',
			'options'  => array(
				array(
					'value' => 'Children’s Party',
					'label' => 'Children’s Party'
				),
				array(
					'value' => "Comedy",
					'label' => "Comedy"
				),
				array(
					'value' => 'Corporate',
					'label' => 'Corporate'
				),
				array(
					'value' => 'Illusionists',
					'label' => 'Illusionists'
				),
				array(
					'value' => 'Mind Readers or Mentalists',
					'label' => 'Mind Readers or Mentalists'
				),
				array(
					'value' => 'Escape Artists',
					'label' => 'Escape Artists'
				)
			)
		);

		return $fields;
	}
}