<?php

namespace Fewlines\Component\Form\Element\Input;

class Checkbox extends Radio
{
	/**
	 * @var string
	 */
	protected $realName;

	/**
	 * @return string
	 */
	public function getRealName()
	{
		return $this->realName;
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->realName = $name;

		if(substr($name, -2) == '[]')
		{
			$this->name = substr($name, 0, -2);
		}
		else
		{
			$this->name = $name;
		}
	}
}