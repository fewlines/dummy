<?php 

namespace Fewlines\Form;

abstract class Element
{
	/**
	 * The name of the element
	 * 
	 * @var string
	 */
	protected $name;

	/**
	 * Defines if the element is required
	 * 
	 * @var boolean
	 */
	protected $required;

	/**
	 * @var integer
	 */
	protected $tabindex;

	/**
	 * @var boolean
	 */
	protected $disabled;

	/**
	 * @param string $name 
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @param boolean|string $isDisabled
	 */
	public function setDisabled($isDisabled)
	{
		$this->disabled = filter_var($isDisabled, FILTER_VALIDATE_BOOLEAN);
	}

	/**
	 * @param boolean|string $isRequired 
	 */
	public function setRequired($isRequired)
	{
		$this->required = filter_var($isRequired, FILTER_VALIDATE_BOOLEAN);
	}

	/**
	 * @param integer|string $tabindex
	 */
	public function setTabindex($tabindex)
	{
		$this->tabindex = (int) $tabindex;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return boolean
	 */
	public function isRequired()
	{
		return $this->required;
	}

	/**
	 * @return boolean
	 */
	public function isDisabled()
	{
		return $this->disabled;
	}
}