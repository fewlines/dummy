<?php 

namespace Fewlines\Form\Validation\Validator;

class Minlength extends \Fewlines\Form\Validation\Validator
{
	/**
	 * @param  string $value 
	 * @return boolean
	 */
	public function validate($value) 
	{
		if(true == is_string($value) && true == is_numeric($this->content))
		{
			if(strlen($value) < intval($this->content))
			{
				return false;
			}
			else
			{
				return true;
			}
		}

		return true;
	}
}