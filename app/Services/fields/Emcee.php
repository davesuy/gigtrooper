<?php

namespace Gigtrooper\Services\fields;


class Emcee
{
	public static function getData()
	{
		$fields = [];


		$fields[] = array(
			'title'   => 'Style',
			'handle'   => 'mcStyle',
			'generate' => true,
			'field'    => 'CheckboxField',
			'options'  => array(
				array(
					'value' => 'Comedy',
					'label' => 'Comedy'
				),
				array(
					'value' => 'Formal',
					'label' => 'Formal'
				)
			)
		);

		return $fields;
	}
}