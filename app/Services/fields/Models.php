<?php

namespace Gigtrooper\Services\fields;


class Models
{
	public static function getData()
	{
		$fields = [];


		$fields[] = array(
			'title'   => 'Gender',
			'handle'   => 'gender',
			'generate' => true,
			'field'    => 'RadiobuttonField',
			'options'  => array(
				array(
					'value' => 'male',
					'label' => 'Male'
				),
				array(
					'value' => 'female',
					'label' => 'Female'
				)
			)
		);

		$fields[] = array(
			'title'   => 'Type',
			'handle'   => 'modelType',
			'generate' => true,
			'field'    => 'CheckboxField',
			'options'  => array(
				array(
					'value' => 'Fashion (Editorial) Model',
					'label' => 'Fashion (Editorial) Model'
				),
				array(
					'value' => 'Runway/Catwalk Model',
					'label' => 'Runway/Catwalk Model'
				),
				array(
					'value' => 'Commercial Model',
					'label' => 'Commercial Model'
				),
				array(
					'value' => 'Plus Size Model',
					'label' => 'Plus Size Model'
				),
				array(
					'value' => 'Petite Model',
					'label' => 'Petite Model'
				),
				array(
					'value' => 'Child Model',
					'label' => 'Child Model'
				),
				array(
					'value' => 'Swimsuit/Lingerie Model',
					'label' => 'Swimsuit/Lingerie Model'
				),
				array(
					'value' => 'Glamour Model',
					'label' => 'Glamour Model'
				),
				array(
					'value' => 'Fitness Model',
					'label' => 'Fitness Model'
				),
				array(
					'value' => 'Fit Model',
					'label' => 'Fit Model'
				),
				array(
					'value' => 'Parts Model',
					'label' => 'Parts Model'
				),
				array(
					'value' => 'Promotional Model',
					'label' => 'Promotional Model'
				),
				array(
					'value' => 'Mature Model',
					'label' => 'Mature Model'
				)
			)
		);

		$fields[] = array(
			'title'   => 'Height',
			'handle'   => 'height',
			'generate' => true,
			'field'    => 'DropdownField',
			'sortOptions' => false,
			'options'  => array(
				array(
					'value' => "5'0",
					'label' => "5'0"
				),
				array(
					'value' => "5'1",
					'label' => "5'1"
				),
				array(
					'value' => "5'2",
					'label' => "5'2"
				),
				array(
					'value' => "5'3",
					'label' => "5'3"
				),
				array(
					'value' => "5'4",
					'label' => "5'4"
				),
				array(
					'value' => "5'5",
					'label' => "5'5"
				),
				array(
					'value' => "5'6",
					'label' => "5'6"
				),
				array(
					'value' => "5'7",
					'label' => "5'7"
				),
				array(
					'value' => "5'8",
					'label' => "5'8"
				),
				array(
					'value' => "5'9",
					'label' => "5'9"
				),
				array(
					'value' => "5'10",
					'label' => "5'10"
				),
				array(
					'value' => "5'11",
					'label' => "5'11"
				),
				array(
					'value' => "6'0",
					'label' => "6'0"
				),
				array(
					'value' => "6'1",
					'label' => "6'1"
				),
				array(
					'value' => "6'2",
					'label' => "6'2"
				),
				array(
					'value' => "6'3",
					'label' => "6'3"
				),
				array(
					'value' => "6'4",
					'label' => "6'4"
				),
				array(
					'value' => "6'6",
					'label' => "6'6"
				),
				array(
					'value' => "6'6",
					'label' => "6'6"
				),
				array(
					'value' => "6'7",
					'label' => "6'7"
				),
				array(
					'value' => "6'8",
					'label' => "6'8"
				),
				array(
					'value' => "6'9",
					'label' => "6'9"
				),
				array(
					'value' => "6'10",
					'label' => "6'10"
				),
				array(
					'value' => "6'11",
					'label' => "6'11"
				),
				array(
					'value' => "7'0",
					'label' => "7'0"
				),
				array(
					'value' => "7'1",
					'label' => "7'1"
				),
				array(
					'value' => "7'2",
					'label' => "7'2"
				)
			)
		);

		return $fields;
	}
}