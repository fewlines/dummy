<?php 

namespace Fewlines\Form\Validation\Validator;

class Regex extends \Fewlines\Form\Validation\Validator
{
	/**
	 * @param  string $value 
	 * @return boolean
	 */
	public function validate($value) 
	{
		if(false == empty($this->content))
		{
			return (bool) @preg_match($this->content, $value);
		}
		
		return true;
	}
}