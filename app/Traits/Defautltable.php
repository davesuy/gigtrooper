<?php

namespace Gigtrooper\Traits;

trait Defautltable
{
	public function initDefaultfieldTypes()
	{
		$fields = array();

		$fields[] = array(
			'handle'   => 'id',
			'field'    => 'ElementField'
		);

		return $fields;
	}
}