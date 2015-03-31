<?php 

namespace Fewlines\Form\Element\Input;

class Text extends \Fewlines\Form\Element\Input
{
	/**
	 * @var integer
	 */
	protected $maxlength;

	/**
	 * @param integer $maxlength
	 */
	public function setMaxlength($maxlength)
	{
		$this->maxlength = (int) $maxlength;
	}

	/**
	 * @return integer
	 */
	public function getMaxlength()
	{
		return $this->maxlength;
	}
}