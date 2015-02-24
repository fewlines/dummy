<?php

namespace Fewlines\Form;

use Fewlines\Form\Element\Input;
use Fewlines\Form\Element\Input\Checkbox as CheckboxInput;
use Fewlines\Form\Element\Input\Password as PasswordInput;
use Fewlines\Form\Element\Input\Radio as RadioInput;
use Fewlines\Form\Element\Input\Submit as SubmitInput;
use Fewlines\Form\Element\Input\Text as TextInput;
use Fewlines\Form\Element\Select;
use Fewlines\Form\Element\Textarea;

class Form
{
	/**
	 * The element taname of the config element
	 * which contains all inputs etc.
	 *
	 * @var string
	 */
	const XML_ELEMENTS_TAG = 'elements';

	/**
	 * @var
	 */
	const SETTER_PREFIX = 'set';

	/**
	 * The config tree
	 *
	 * @var \Fewlines\Xml\Tree\Element|null
	 */
	private $config = null;

	/**
	 * Identifer for the form
	 *
	 * @var string
	 */
	private $name = '';

	/**
	 * The method of the form
	 *
	 * @var string
	 */
	private $method = 'post';

	/**
	 * The type to open the action
	 *
	 * @var string
	 */
	private $target = '_self';

	/**
	 * Charset used in the submitted form
	 *
	 * @var string
	 */
	private $acceptCharset = 'UTF-8';

	/**
	 * Tells if the browser will check the form after
	 * a submit
	 *
	 * @var boolean
	 */
	private $noValidate = false;

	/**
	 * Telss if autocomplete should be
	 * allowd or not
	 *
	 * @var string
	 */
	private $autoComplete = 'on';

	/**
	 * The desitnation file after a submit
	 *
	 * @var string
	 */
	private $action = '';

	/**
	 * How the data will be encoded after
	 * submitting it
	 *
	 * Types:
	 * 	- application/x-www-form-urlencoded
	 *  - multipart/form-data
	 *  - text/plain
	 *
	 * @var string
	 */
	private $encType = 'application/x-www-form-urlencoded';

	/**
	 * The elements in the formular
	 *
	 * @var array
	 */
	private $elements = array();

	/**
	 * Init a form (with a given xml config)
	 *
	 * @param \Fewlines\Xml\Tree\Element|null $config
	 */
	public function __construct(\Fewlines\Xml\Tree\Element $config = null)
	{
		if(true == $config instanceof \Fewlines\Xml\Tree\Element)
		{
			$this->config = $config;

			// Get form items defined in the xml config
			$elements = $this->config->getChildByName(self::XML_ELEMENTS_TAG);

			// Add the form elements from the config as element
			if(false != $elements && $elements->countChildren() > 0)
			{
				$this->addElementsByXmlConfig($elements->getChildren());
			}
		}

		pr($this->elements);
	}

	/**
	 * Adds elements from a xml config
	 * @param array $elements
	 */
	private function addElementsByXmlConfig($elements)
	{
		foreach($elements as $element)
		{
			switch(strtolower($element->getName()))
			{
				case Input::HTML_TAG:
					$type       = $element->getAttribute('type');
					$inputName  = $element->getAttribute('name');
					$attributes = $element->getAttributes(array('name'));

					if(false == empty($type) &&
						false == empty($inputName))
					{
						$this->addElement(Input::HTML_TAG, $inputName, $attributes);
					}
				break;

				case Select::HTML_TAG:
					$inputName  = $element->getAttribute('name');
					$options    = $element->getChildrenByName('option');
					$attributes = $element->getAttributes(array('name'));
					$attributes['options'] = $options;

					if(false == empty($inputName))
					{
						$this->addElement(Select::HTML_TAG, $inputName, $attributes);
					}
				break;

				case Textarea::HTML_TAG:

				break;
			}
		}
	}

	/**
	 * Adds a form item to the
	 * formular
	 *
	 * @param string $type
	 * @param string $name
	 * @param array  $attributes
	 */
	public function addElement($type, $name, $attributes = array())
	{
		$element = null;

		switch(strtolower($type))
		{
			case Input::HTML_TAG:
				if(false == array_key_exists('type', $attributes))
				{
					return;
				}

				$class  = __NAMESPACE__ . "\\Element\\Input\\";
				$class .= ucfirst($attributes['type']);

				$element = new $class;

				// Set element name
				$element->setName($name);

				// Set other attributes
				$this->addElementAttributes($element, $attributes);
			break;

			case Select::HTML_TAG:
				$class = __NAMESPACE__ . "\\Element\\Select";
				$element = new $class;

				if(array_key_exists('options', $attributes) &&
					is_array($attributes['options']))
				{
					$options = $attributes['options'];

					for($i = 0, $len = count($options); $i < $len; $i++)
					{
						$content  = (string) $options[$i];
						$value    = $options[$i]->getAttribute("value");
						$selected = $options[$i]->getAttribute("selected");

						if(empty($selected))
						{
							$selected = "false";
						}

						$element->addOption(Select::createOption($content, $value, $selected));
					}

					unset($attributes['options']);
				}

				// Set name of the select field
				$element->setName($name);

				// Add all other attributes
				$this->addElementAttributes($element, $attributes);
			break;

			case Textarea::HTML_TAG:

			break;
		}

		if(false == is_null($element))
		{
			$this->elements[] = $element;
		}
	}

	/**
	 * @param *     $elementf
	 * @param array $attributes
	 */
	public function addElementAttributes($element, $attributes)
	{
		foreach($attributes as $name => $value)
		{
			$method = self::SETTER_PREFIX . ucfirst($name);

			if(true == method_exists($element, $method))
			{
				$element->{$method}($value);
			}
		}
	}
}