<?php

namespace Fewlines\Form;

class Validation
{
	/**
	 * @var array
	 */
	private $options = array();

	/**
	 * @var array
	 */
	private $errors = array();

	/**
	 * @param array|\Fewlines\Xml\Tree\Element $errors
	 * @param array|\Fewlines\Xml\Tree\Element $options
	 */
	public function __construct($errors = array(), $options = array())
	{
		// Use xml object to set the errors
		if(true == ($errors instanceof \Fewlines\Xml\Tree\Element))
		{
			foreach($errors->getChildren() as $child)
			{
				$this->addError($child->getName(), $child->getContent());
			}
		}

		// Use xml object to set the options
		// of the validation
		if(true == ($options instanceof \Fewlines\Xml\Tree\Element))
		{
			foreach($options->getAttributes() as $type => $value)
			{
				$this->addOption($type, $value);
			}
		}
	}

	/**
	 * @param string $type
	 * @param string $value
	 */
	public function addOption($type, $value = "")
	{
		// ...
	}

	/**
	 * @param string $type
	 * @param string $message
	 */
	public function addError($type, $message = "")
	{
		if(true == empty($type))
		{
			throw new Exception\InvalidErrorValidationTypeException(
				"No valid type given to create an error object"
			);
		}

		$this->errors[] = new Validation\Error($type, $message);
	}
}