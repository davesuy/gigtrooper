<?php

namespace Gigtrooper\Services\fields;


class Service
{
	public static function getData()
	{
		$fields = [];


		$fields[] = array(
			'title'   => 'Decor Service',
			'handle'   => 'decorService',
			'generate' => true,
			'field'    => 'CheckboxField',
			'options'  => array(
				array(
					'value' => 'Balloon Decor',
					'label' => 'Balloon Decor'
				),
				array(
					'value' => "Event Florists",
					'label' => "Event Florists"
				),
				array(
					'value' => 'Interior Decorators',
					'label' => 'Interior Decorators'
				),
				array(
					'value' => 'Party Decor',
					'label' => 'Party Decor'
				)
			)
		);

		$fields[] = array(
			'title'   => 'Photo And Video Service',
			'handle'   => 'photoVideoService',
			'generate' => true,
			'field'    => 'CheckboxField',
			'options'  => array(
				array(
					'value' => "Photo Booths",
					'label' => "Photo Booths"
				),
				array(
					'value' => 'Videographers',
					'label' => 'Videographers'
				)
			)
		);

		$fields[] = array(
			'title'   => 'Has Drone',
			'handle'   => 'hasDrone',
			'generate' => true,
			'field'    => 'RadiobuttonField',
			'options'  => array(
				array(
					'value' => 'no',
					'label' => 'No'
				),
				array(
					'value' => 'yes',
					'label' => 'Yes'
				)
			)
		);


		$fields[] = array(
			'title'   => 'Inclusions',
			'handle'   => 'makeupInclusions',
			'generate' => true,
			'field'    => 'CheckboxField',
			'options'  => array(
				array(
					'value' => 'Does Hair',
					'label' => 'Does Hair'
				),
				array(
					'value' => 'Airbrush Makeup',
					'label' => 'Airbrush Makeup'
				),
			)
		);

		$fields[] = array(
			'title'   => 'Does Hair',
			'handle'   => 'doesHair',
			'generate' => true,
			'field'    => 'CheckboxField',
			'options'  => array(
				array(
					'value' => 'Does Hair',
					'label' => 'Does Hair'
				)
			)
		);

		$fields[] = array(
			'title'   => 'Does Makeup',
			'handle'   => 'doesMakeup',
			'generate' => true,
			'field'    => 'CheckboxField',
			'options'  => array(
				array(
					'value' => 'Does Makeup',
					'label' => 'Does Makeup'
				)
			)
		);

		return $fields;
	}
}