<?php

namespace Fewlines\Form\Validation\Validator;

class Mincount extends \Fewlines\Form\Validation\Validator
{
	/**
	 * @param  array $value
	 * @return boolean
	 */
	public function validate($value)
	{
		if(true == is_numeric($this->content))
		{
			if(true == is_array($value))
			{
				if(count($value) < intval($this->content))
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}

		return true;
	}
}