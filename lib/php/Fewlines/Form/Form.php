<?php

namespace Fewlines\Form;

use Fewlines\Form\Element\Input;
use Fewlines\Form\Element\Select;
use Fewlines\Form\Element\Textarea;
use Fewlines\Helper\ParseContentHelper;

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
	 * @var array
	 */
	private $attributes = array();

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
			$this->setFormAttributesByConfig();
	
			// Get form items defined in the xml config
			$elements = $this->config->getChildByName(self::XML_ELEMENTS_TAG);

			// Add the form elements from the config as element
			if(false != $elements && $elements->countChildren() > 0)
			{
				$this->addElementsByXmlConfig($elements->getChildren());
			}
		}
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param string $method 
	 */
	public function setMethod($method)
	{
		$method = strtolower($method);

		if($method != 'post' && $method != 'get')
		{
			throw new Exception\MethodDoesNotExistException("
				The method \"" . $method . "\" does not exist.
				Use \"POST\" or \"GET\".
			");
		}

		$this->method = $method;
	}

	/**
	 * @return string
	 */
	public function getMethod()
	{
		return $this->method;
	}

	/**
	 * @param string $target 
	 */
	public function setTarget($target)
	{
		$this->target = $target;
	}

	/**
	 * @return string
	 */
	public function getTarget()
	{
		return $this->target;
	}

	/**
	 * @param string $charset
	 */
	public function setAcceptCharset($charset)
	{
		$this->acceptCharset = $charset;
	}

	/**
	 * @return string
	 */
	public function getAcceptCharset()
	{
		return $this->acceptCharset;
	}

	/**
	 * @param boolean|string $noValidate 
	 */
	public function setNoValidate($noValidate)
	{
		$this->noValidate = filter_var($noValidate, FILTER_VALIDATE_BOOLEAN);
	}

	/**
	 * @return boolean
	 */
	public function isNovalidate()
	{
		return $this->noValidate;
	}

	/**
	 * @param string $autoComplete
	 */
	public function setAutoComplete($autoComplete)
	{
		$autoComplete = strtolower($autoComplete);

		if($autoComplete != 'on' && $autoComplete != 'off')
		{
			throw new Exception\AutoCompleteValueInvalidException("
				Please define a valid trigger. ON or OFF.
			");
		}

		$this->autoComplete = $autoComplete;
	}

	/**
	 * @param string $action
	 */
	public function setAction($action)
	{
		$this->action = $action;
	}	

	/**
	 * @return string
	 */
	public function getAction()
	{
		return $this->action;
	}
	
	/**
	 * @param string $encType
	 */
	public function setEncType($encType)
	{
		$encType = strtolower($encType);

		if($encType != 'application/x-www-form-urlencoded' && 
		   	$encType != 'multipart/form-data' && 
		   	$encType != 'text/plain')
	   {
	   		throw new Exception\InvalidEncTypeException("
				Please enter a valid enctype for the 
				formular.
	   		");
	   }

	   $this->encType = $encType;
	}

	/**
	 * @return string
	 */
	public function getEncType()
	{
		return $this->encType;
	}

	/**
	 * @param string $name
	 * @param string $content
	 */
	public function addAttribute($name, $content)
	{
		$this->attributes[$name] = $content;
	}

	/**
	 * @return array
	 */
	public function getAttributes()
	{
		return $this->attributes;
	}

	/**
	 * @param  string $name
	 * @return string     
	 */
	public function getAttribute($name)
	{	
		if(array_key_exists($name, $this->attributes))
		{
			return $this->attributes[$name];
		}

		return '';
	}

	/**
	 * Adds the attributes from the given config 
	 * to itself
	 */
	private function setFormAttributesByConfig()
	{	
		if($this->config instanceof \Fewlines\Xml\Tree\Element)
		{
			foreach($this->config->getAttributes() as $name => $content)
			{
				ParseContentHelper::parseLine($content);

				switch(strtolower($name))
				{
					case 'name':
						$this->setName($content);
					break;

					case 'method':
						$this->setMethod($content);
					break;

					case 'target':
						$this->setTarget($content);
					break;

					case 'accept-charset':
						$this->setAcceptCharset($content);
					break;

					case 'novalidate':
						$this->setNoValidate($content);
					break;

					case 'autocomplete':
						$this->setAutoComplete($content);
					break;

					case 'action':
						$this->setAction($content);
					break;

					case 'enctype':
						$this->setEncType($content);
					break;

					default:
						$this->addAttribute($name, $content);
					break;
				}
			}	
		}
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
					$inputName  = $element->getAttribute('name');
					$attributes = $element->getAttributes(array('name'));
					$attributes['content'] = (string) $element;

					if(false == empty($inputName))
					{
						$this->addElement(Textarea::HTML_TAG, $inputName, $attributes);
					}
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
				$class   = __NAMESPACE__ . "\\Element\\Select";
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
				$class   = __NAMESPACE__ . "\\Element\\Textarea";
				$element = new $class;	

				// Set name
				$element->setName($name);

				// Add attributes
				$this->addElementAttributes($element, $attributes);
			break;
		}

		if(false == is_null($element))
		{
			$this->elements[] = $element;
		}
	}

	/**
	 * @param *     $element
	 * @param array $attributes
	 */
	public function addElementAttributes($element, $attributes)
	{
		foreach($attributes as $name => $content)
		{
			$method = self::SETTER_PREFIX . ucfirst($name);

			// Parse content
			// ParseContentHelper::parseLine($content);

			if(true == method_exists($element, $method))
			{
				$element->{$method}($content);
			}
			else
			{
				$element->addAttribute($name, $content);
			}
		}
	}
}