<?php

namespace Fewlines\Form\Element;

class Select extends \Fewlines\Form\Element
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
	}

	/**
	 * @param string $option
	 */
	public function addOption($option)
	{
		$this->options[] = $option;
	}

	/**
	 * @param  string $content
	 * @param  string $value
	 * @param  string|boolean $selected
	 * @return \Fewlines\Form\Element\Select\Option
	 */
	public static function createOption($content, $value, $selected)
	{
		$option = new Select\Option;

		$option->setContent($content);
		$option->setValue($value);
		$option->setSelected($selected);

		return $option;
	}
}