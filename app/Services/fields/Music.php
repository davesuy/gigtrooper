<?php

namespace Gigtrooper\Services\fields;


class Music
{
	public static function getData()
	{
		$fields = [];

		$fields[] = array(
			'title'   => 'Band Genre',
			'handle'   => 'bandGenre',
			'generate' => true,
			'field'    => 'CheckboxField',
			'options'  => array(
				array(
					'value' => 'Pop',
					'label' => 'Pop'
				),
				array(
					'value' => "Alternative",
					'label' => "Alternative"
				),
				array(
					'value' => 'Jazz',
					'label' => 'Jazz'
				),
				array(
					'value' => 'Karaoke',
					'label' => 'Karaoke'
				),
				array(
					'value' => 'Heavy Metal',
					'label' => 'Heavy Metal'
				),
				array(
					'value' => 'Oldies',
					'label' => 'Oldies'
				),
				array(
					'value' => 'One Man',
					'label' => 'One Man'
				),
				array(
					'value' => 'Party',
					'label' => 'Party'
				),
				array(
					'value' => 'Punk',
					'label' => 'Punk'
				),
				array(
					'value' => 'Rock',
					'label' => 'Rock'
				),
				array(
					'value' => 'Reggae',
					'label' => 'Reggae'
				),
				array(
					'value' => 'Bossa Nova',
					'label' => 'Bossa Nova'
				),
				array(
					'value' => 'Christian And Gospel',
					'label' => 'Christian And Gospel'
				)
			)
		);

		$fields[] = array(
			'title'   => 'Brings Equipment',
			'handle'   => 'BringsEquipment',
			'generate' => true,
			'field'    => 'RadiobuttonField',
			'options'  => array(
				array(
					'value' => 'yes',
					'label' => 'Yes'
				),
				array(
					'value' => 'no',
					'label' => 'No'
				)
			)
		);


		$fields[] = array(
			'title'   => 'Instruments',
			'handle'   => 'instruments',
			'generate' => true,
			'field'    => 'CheckboxField',
			'options'  => array(
				array(
					'value' => 'Drummer',
					'label' => 'Drummer'
				),
				array(
					'value' => "Guitarist/Acoustic",
					'label' => "Guitarist"
				),
				array(
					'value' => 'Pianist',
					'label' => 'Pianist'
				),
				array(
					'value' => 'Keyboard Player',
					'label' => 'Keyboard Player'
				),
				array(
					'value' => 'Saxophone',
					'label' => 'Saxophone'
				),
				array(
					'value' => 'Trumpet',
					'label' => 'Trumpet'
				),
				array(
					'value' => 'Violinist',
					'label' => 'Violinist'
				)
			)
		);

		$fields[] = array(
			'title'   => 'DJ Genre',
			'handle'   => 'djGenre',
			'generate' => true,
			'field'    => 'CheckboxField',
			'options'  => array(
				array(
					'value' => 'Club',
					'label' => 'Club'
				),
				array(
					'value' => "Event",
					'label' => "Event"
				),
				array(
					'value' => 'Prom',
					'label' => 'Prom'
				),
				array(
					'value' => 'Radio',
					'label' => 'Radio'
				),
				array(
					'value' => 'Wedding',
					'label' => 'Wedding'
				)
			)
		);

		return $fields;
	}
}