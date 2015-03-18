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
	 * @var array
	 */
	private $validators = array();

	/**
	 * @param array|\Fewlines\Xml\Tree\Element $errors
	 * @param array|\Fewlines\Xml\Tree\Element $options
	 */
	public function __construct($errors = array(), $options = array())
	{
		// Use xml object to set the errors
		// Otherwise use an array (Manually)
		if(true == ($errors instanceof \Fewlines\Xml\Tree\Element))
		{
			foreach($errors->getChildren() as $child)
			{
				$this->addError($child->getName(), $child->getContent());
			}
		}
		else if(true == is_array($errors))
		{
			foreach($errors as $type => $message)
			{
				$this->addError($type, $message);
			}
		}

		// Use xml object to set the options
		// of the validation
		// Otherwise use an array (Manually)
		if(true == ($options instanceof \Fewlines\Xml\Tree\Element))
		{
			foreach($options->getAttributes() as $type => $value)
			{
				$this->addOption($type, $value);
			}
		}
		else if(true == is_array($options))
		{
			foreach($options as $type => $value)
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
		if(true == empty($type))
		{
			throw new Exception\InvalidOptionValidationTypeException(
				"No valid type given to create an option object"
			);
		}

		$option    = new Validation\Option($type, $value);
		$validator = $this->createValidatorByOption($option);

		if($validator != false)
		{
			$this->validators[$validator->getType()] = $validator;
		}

		$this->options[] = $option;
	}

	/**
	 * @param  \Fewlines\Form\Validation\Option $option
	 * @return \Fewlines\Form\Validation\Validator|boolean
	 */
	private function createValidatorByOption(\Fewlines\Form\Validation\Option $option) 
	{
		$class = "\\" . __NAMESPACE__ . "\\Validation\\Validator\\" . ucfirst(trim($option->getType()));

		if(true === class_exists($class))
		{
			return new $class($option->getValue());
		}

		return false;
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

	/**
	 * @param  string $value 
	 * @return 
	 */
	public function validate($value)
	{
		foreach($this->validators as $type => $validator)
		{
			echo $type . ": ";
			var_dump($validator->validate($value));
			echo "<br />";
		}
	}
}