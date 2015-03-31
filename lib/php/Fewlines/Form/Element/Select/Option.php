<?php

namespace Fewlines\Form\Element\Select;

class Option extends \Fewlines\Dom\Element
{
	/**
	 * @var string
	 */
	protected $value;

	/**
	 * @var string
	 */
	protected $content;

	/**
	 * @var boolean
	 */
	protected $selected;

	/**
	 * Set Dom configs
	 */
	public function __construct()
	{
		$this->setDomStr(self::OPTION_STR);
		$this->setDomTag(self::OPTION_TAG);
	}

	/**
	 * @param string $content
	 */
	public function setContent($content)
	{
		$this->content = $content;
	}

	/**
	 * @return string
	 */
	public function getContent()
	{
		return $this->content;
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
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @param boolean|string $isSelected
	 */
	public function setSelected($isSelected)
	{
		$this->selected = filter_var($isSelected, FILTER_VALIDATE_BOOLEAN);
	}

	/**
	 * @return boolean
	 */
	public function isSelected()
	{
		return $this->selected;
	}
}