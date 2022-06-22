<?php

namespace Gigtrooper\Services\fields;


class Singers
{
	public static function getData()
	{
		$fields = [];

		$fields[] = array(
			'title'   => 'Singer Style',
			'handle'   => 'singerStyle',
			'generate' => true,
			'field'    => 'CheckboxField',
			'options'  => array(
				array(
					'value' => 'Pop',
					'label' => 'Pop'
				),
				array(
					'value' => 'Rock',
					'label' => 'Rock'
				),
				array(
					'value' => 'Jazz',
					'label' => 'Jazz'
				),
				array(
					'value' => 'Rap',
					'label' => 'Rap'
				),
				array(
					'value' => 'Broadway Style',
					'label' => 'Broadway Style'
				),
				array(
					'value' => "Children's Music",
					'label' => "Children's Music"
				),
				array(
					'value' => 'Classical Singers',
					'label' => 'Classical Singers'
				),
				array(
					'value' => 'Country Singers',
					'label' => 'Country Singers'
				)
			)
		);

		return $fields;
	}
}