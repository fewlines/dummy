<?php

namespace Fewlines\Form\Validation;

class Option
{
	/**
	 * The type of the option.
	 * E.g. regex
	 *
	 * @var string
	 */
	private $type;

	/**
	 * The value of the option
	 * to check
	 *
	 * @var string
	 */
	private $value;

	/**
	 * @param string $type
	 * @param string $value
	 */
	public function __construct($type, $value = "")
	{
		$this->type = $type;

		if($value == "false" || $value == "true") 
		{
			$value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
		}

		$this->value = $value;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @return string
	 */
	public function getValue()
	{
		return $this->value;
	}
}