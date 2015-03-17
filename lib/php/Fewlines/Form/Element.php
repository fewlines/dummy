<?php

namespace Fewlines\Form;

abstract class Element extends \Fewlines\Dom\Element
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
	 * @var boolean
	 */
	protected $readonly;

	/**
	 * @var array
	 */
	protected $classes;

	/**
	 * @var string
	 */
	protected $id;

	/**
	 * @var \Fewlines\Form\Validation
	 */
	protected $validation;

	/**
	 * @var array
	 */
	protected $attributes = array();

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
	 * @return boolean
	 */
	public function isDisabled()
	{
		return $this->disabled;
	}

	/**
	 * @param boolean|string $isRequired
	 */
	public function setRequired($isRequired)
	{
		$this->required = filter_var($isRequired, FILTER_VALIDATE_BOOLEAN);
	}

	/**
	 * @param boolean $isReadonly
	 */
	public function setReadonly($isReadonly)
	{
		$this->readonly = filter_var($isReadonly, FILTER_VALIDATE_BOOLEAN);
	}

	/**
	 * @return boolean
	 */
	public function isReadonly()
	{
		return $this->readonly;
	}

	/**
	 * @param integer|string $tabindex
	 */
	public function setTabindex($tabindex)
	{
		$this->tabindex = (int) $tabindex;
	}

	/**
	 * @return integer
	 */
	public function getTabindex()
	{
		return $this->tabindex;
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
	 * @param string|array $classes
	 */
	public function setClasses($classes)
	{
		if(true == is_array($classes))
		{
			$this->classes = $classes;
		}
		else
		{
			$this->classes = explode(" ", $classes);
		}
	}

	/**
	 * @return array
	 */
	public function getClasses()
	{
		return $this->classes;
	}

	/**
	 * @param string $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
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
	 * @return array
	 */
	public function getAttributes()
	{
		return $this->attributes;
	}

	/**
	 * @param array|\Fewlines\Xml\Tree\Element $errors
	 * @param array|\Fewlines\Xml\Tree\Element $options
	 */
	public function setValidation($errors, $options)
	{
		$this->validation = new Validation($errors, $options);
	}

	/**
	 * @return \Fewlines\Form\Validation
	 */
	public function getValidation()
	{
		return $this->validation;
	}
}