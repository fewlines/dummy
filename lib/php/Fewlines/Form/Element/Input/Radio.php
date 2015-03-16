<?php 

namespace Fewlines\Form\Element\Input;

class Radio extends \Fewlines\Form\Element\Input
{
	/**
	 * @var string
	 */
	protected $value;

	/**
	 * @var boolean
	 */
	protected $checked;

	/**
	 * @return string
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @param string $value
	 */
	public function setValue($value)
	{
		$this->value = $value;
	}

	/**
	 * @return string
	 */
	public function isChecked()
	{
		return $this->checked;
	}

	/**
	 * @param string $checked
	 */
	public function setChecked($checked)
	{
		$this->checked = filter_var($checked, FILTER_VALIDATE_BOOLEAN);
	}	
}