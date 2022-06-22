<?php

namespace Gigtrooper\Fields;

class OptionData
{
	// Properties
	// =========================================================================

	/**
	 * @var string
	 */
	public $label;

	/**
	 * @var string
	 */
	public $value;

	/**
	 * @var
	 */
	public $selected;

	// Public Methods
	// =========================================================================

	/**
	 * Constructor
	 *
	 * @param string $label
	 * @param string $value
	 * @param        $selected
	 *
	 * @return OptionData
	 */
	public function __construct($label, $value, $selected)
	{
		$this->label    = $label;
		$this->value    = $value;
		$this->selected = $selected;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->value;
	}
}
