<?php

namespace Fewlines\Component\Form\Element;

class Select extends \Fewlines\Component\Form\Element
{
	/**
	 * @var string
	 */
	const HTML_TAG = 'select';

	/**
	 * @var array
	 */
	protected $options = array();

	/**
	 * Define dom elements type
	 */
	public function __construct()
	{
		$this->setDomTag(self::SELECT_TAG);
		$this->setDomStr(self::SELECT_STR);
	}

	/**
	 * @return array
	 */
	public function getOptions()
	{
		return $this->options;
	}

	/**
	 * @param array $options
	 */
	public function setOptions($options)
	{
		if(false == is_array($options))
		{
			throw new Exception\SelectOptionsNoArayException("
				The options given are not an array.
			");
		}

		$this->options = $options;

		// Add childs to dom
		foreach($options as $option)
		{
			$this->addChild($option);
		}
	}

	/**
	 * @param string $option
	 */
	public function addOption($option)
	{
		$this->options[] = $option;

		// Add child to dom
		$this->addChild($option);
	}

	/**
	 * @param  string $content
	 * @param  string $value
	 * @param  string|boolean $selected
	 * @return \Fewlines\Component\Form\Element\Select\Option
	 */
	public static function createOption($content, $value, $selected)
	{
		$option = new Select\Option;

		$option->setContent($content);
		$option->setValue($value);
		$option->setSelected($selected);

		// Set option as attributes for the dom element
		// to render
		$option->setAttributes(array(
				'value'    => $value,
				'selected' => $selected
			));

		return $option;
	}
}