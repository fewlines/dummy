<?php 

namespace Fewlines\Form\Validation\Validator;

class Base extends \Fewlines\Form\Validation\Validator
{
	/**
	 * @var string
	 */
	protected $value;

	/**
	 * @return boolean
	 */
	protected function isEmpty()
	{
		return !(trim(preg_replace('/ |\t|\r|\r\n/', '', $this->value)) == '');
	}
}