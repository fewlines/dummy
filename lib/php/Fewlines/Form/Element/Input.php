<?php 

namespace Fewlines\Form\Element;

class Input extends \Fewlines\Form\Element
{
	/**
	 * @var string
	 */
	const HTML_TAG = 'input';

	/**
	 * @var string
	 */
	protected $type;

	/**
	 * @var string
	 */
	protected $placeholder;

	/**
	 * @var boolean
	 */
	protected $readonly;

	/**
	 * @var string
	 */
	protected $value;

	/**
	 * @var integer
	 */
	protected $size;

	/**
	 * For image types
	 * 
	 * @var string
	 */
	protected $src;

	/**
	 * For image types
	 * 
	 * @var boolean
	 */
	protected $ismap;

	/**
	 * For image types
	 * 
	 * @var string
	 */
	protected $usemap;

	/**
	 * For image types
	 * 
	 * @var string
	 */
	protected $alt;

	/**
	 * @param string $placeholder
	 */
	public function setPlaceholder($placeholder)
	{
		$this->placeholder = $placeholder;
	}

	/**
	 * @return string
	 */
	public function getPlaceholder()
	{
		return $this->placeholder;
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
	 * @param string $type
	 */
	public function setType($type)
	{
		$this->type = $type;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param string $type
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
	 * @param string $src
	 */
	public function setSrc($src)
	{
		$this->src = $src;
	}

	/**
	 * @return string
	 */
	public function getSrc()
	{
		return $this->src;
	}

	/**
	 * @param string|integer $size
	 */
	public function setSize($size)
	{
		$this->size = (int) $size;
	}

	/**
	 * @return string
	 */
	public function getSize()
	{
		return $this->size;
	}	

	/**
	 * @param string|boolean $ismap
	 */
	public function setIsmap($ismap)
	{
		$this->ismap = filter_var($ismap, FILTER_VALIDATE_BOOLEAN);
	}

	/**
	 * @return boolean
	 */
	public function getIsmap()
	{
		return $this->ismap;
	}

	/**
	 * @param string $usemap
	 */
	public function setUsemap($usemap)
	{
		$this->usemap = $usemap;
	}

	/**
	 * @return string
	 */
	public function getUsemap()
	{
		return $this->usemap;
	}	

	/**
	 * @param string $alt
	 */
	public function setAlt($alt)
	{
		$this->alt = $alt;
	}

	/**
	 * @return string
	 */
	public function getAlt()
	{
		return $this->alt;
	}
}