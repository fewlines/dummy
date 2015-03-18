<?php

namespace Fewlines\Form\Validation;

abstract class Validator
{
	/**
	 * @var string
	 */
	protected $content;

	/**
	 * @param string|boolean|number $content 
	 */
	public function __construct($content)
	{	
		$this->content = $content;
	}

	/**
	 * Returns the type of the validator
	 * @return string
	 */
	public function getType()
	{
		return strtolower(end(explode('\\', get_class($this))));
	}

	/**
	 * @param  string $value
	 * @return boolean
	 */
	abstract public function validate($value);
}