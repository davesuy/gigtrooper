<?php

namespace Gigtrooper\Services\fields;


class Dancers
{
	public static function getData()
	{
		$fields = [];

		$fields[] = array(
			'title'   => 'Genre',
			'handle'   => 'dancerGenre',
			'generate' => true,
			'field'    => 'CheckboxField',
			'options'  => array(
				array(
					'value' => 'Hip Hop',
					'label' => 'Hip Hop'
				),
				array(
					'value' => 'Street Dance',
					'label' => 'Street Dance'
				),
				array(
					'value' => 'Modern',
					'label' => 'Modern'
				),
				array(
					'value' => "Folk",
					'label' => "Folk"
				),
				array(
					'value' => 'Zumba',
					'label' => 'Zumba'
				),
				array(
					'value' => 'Ballroom',
					'label' => 'Ballroom'
				)
			)
		);

		return $fields;
	}
}