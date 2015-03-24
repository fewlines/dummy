<?php

namespace Fewlines\Form;

class Result
{
	/**
	 * Indicates if the whole
	 * validation of a formular
	 * is valid and without errors
	 *
	 * @var boolean
	 */
	private $success = true;

	/**
	 * Alle errors from the
	 * elements validations
	 *
	 * @var array
	 */
	private $errors = array();

	/**
	 * @return boolean
	 */
	public function isSuccess()
	{
		return $this->success;
	}

	/**
	 * Adds a result to the
	 * stack
	 *
	 * @param string $name
	 * @param array  $errors
	 */
	public function addError($name, $errors)
	{
		if(false == empty($errors))
		{
			$this->errors[$name] = $errors;

			if($this->success == true)
			{
				$this->success = false;
			}
		}
	}

	/**
	 * Converts all necessary result
	 * innformations to a json string
	 *
	 * @return string
	 */
	public function toJSON()
	{
		return json_encode(array(
				'success' => $this->success,
				'errors'  => $this->errors
			));
	}

	/**
	 * @return array
	 */
	public function getErrors()
	{
		return $this->errors;
	}
}