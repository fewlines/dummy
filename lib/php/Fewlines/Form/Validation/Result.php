<?php 

namespace Fewlines\Form\Validation;

class Result
{
	/**
	 * @var array
	 */
	private $validations = array();

	/**
	 * @return array
	 */
	public function getValidations()
	{
		return $this->validations;
	}

	public function setValidation($option, $message)
	{
		$this->validations[$option] = $message;
	}
}