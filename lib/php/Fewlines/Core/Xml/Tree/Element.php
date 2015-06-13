<?php

namespace Fewlines\Core\Xml\Tree;

use Fewlines\Core\Helper\ArrayHelper;

class Element
{
	/**
	 * The key for all attributes
	 * of a SimpleXmlElement
	 *
	 * @var string
	 */
	const ATTRIBUTE_KEY = '@attributes';

	/**
	 * The (tag)name of the element
	 *
	 * @var string
	 */
	private $name;

	/**
	 * The content of this element
	 *
	 * @var string
	 */
	private $content;

	/**
	 * Holds all attributes of
	 * the element
	 *
	 * @var array
	 */
	private $attributes = array();

	/**
	 * Holds an array of all child elements in the
	 * first layer
	 *
	 * @var array
	 */
	private $children = array();

	/**
	 * Creates a element
	 *
	 * @param string $name
	 * @param array  $attributes
	 * @param string $content
	 */
	public function __construct($name, $attributes, $content = "")
	{
		$this->name       = $name;
		$this->attributes = $this->addAttributes($attributes);
		$this->content    = $content;
	}

	/**
	 * Returns the content of this element
	 * if it's parsed as string
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->content;
	}

	/**
	 * Transforms the attributes
	 * to a valid array
	 *
	 * @param  array $attributes
	 * @return array
	 */
	private function addAttributes($attributes)
	{
		if(is_array($attributes) &&
			array_key_exists(self::ATTRIBUTE_KEY, $attributes))
		{
			return $attributes[self::ATTRIBUTE_KEY];
		}

		return array();
	}

	/**
	 * Adds a child to this element
	 *
	 * @param \Fewlines\Core\Xml\Element $child
	 */
	public function addChild(\Fewlines\Core\Xml\Tree\Element $child)
	{
		$this->children[] = $child;
	}

	/**
	 * Returns the name of the element (node)
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Get the content of this element
	 * Child elements not included
	 *
	 * @return string
	 */
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * Sets the content of the element
	 *
	 * @param string $content
	 */
	public function setContent($content)
	{
		$this->content = $content;
	}

	/**
	 * Checks if the element has the given
	 * attribute name
	 *
	 * @return boolean
	 */
	public function hasAttribute($name) {
		return (bool)array_key_exists($name, $this->attributes);
	}

	/**
	 * Returns a all attributes
	 *
	 * @param  array $exclude
	 * @return array
	 */
	public function getAttributes($exclude = array())
	{
		$attributes = $this->attributes;

		if(false == empty($exclude))
		{
			foreach($exclude as $value)
			{
				if(array_key_exists($value, $attributes))
				{
					unset($attributes[$value]);
				}
			}
		}

		return $attributes;
	}

	/**
	 * Adds a attribute with a name and value
	 *
	 * @param string $name
	 * @param string $value
	 */
	public function addAttribute($name, $value = '') {
		$this->attributes[$name] = $value;
	}

	/**
	 * Get one attribute (if exists)
	 *
	 * @param  string $key
	 * @return string
	 */
	public function getAttribute($key)
	{
		if(array_key_exists($key, $this->attributes))
		{
			return $this->attributes[$key];
		}

		return '';
	}

	/**
	 * Returns all children
	 *
	 * @return array
	 */
	public function getChildren()
	{
		return $this->children;
	}

	/**
	 * Returns the number of the children
	 *
	 * @return integer
	 */
	public function countChildren()
	{
		return count($this->children);
	}

	/**
	 * Gets a list of all elements with this name.
	 * Recursive strategy enabled by default.
	 * It also collects all results if the
	 * parameter is set to true (Without reference
	 * variables)
	 *
	 * @param  string  $name
	 * @param  boolean $recursive
	 * @param  boolean $collect
	 * @return \Fewlines\Core\Xml\Tree\Element|boolean|array
	 */
	public function getChildrenByName($name, $collect = true, $recursive = true)
	{
		$children = array();

		for($i = 0; $i < count($this->children); $i++)
		{
			$child = $this->children[$i];

			if($child->getName() == $name)
			{
 				if(false == $collect)
 				{
 					return $child;
 				}
 				else
 				{
 					$children[] = $child;
 				}
			}
			else if($child->countChildren() > 0 &&
				true == $recursive)
			{
				if(false == $collect)
				{
					return $child->getChildByName($name);
				}
				else
				{
					$depthChilds = $child->getChildByName($name, $collect);

					if(false == empty($depthChilds))
					{
						$children[] = $depthChilds;
					}
				}
			}
		}

		if(true == $collect)
		{
			return ArrayHelper::flatten($children);
		}

		return false;
	}

	/**
	 * Gets a children by the name
	 *
	 * @param  string  $name
	 * @param  boolean $recursive
	 * @return array
	 */
	public function getChildByName($name, $recursive = true)
	{
		return $this->getChildrenByName($name, false, $recursive);
	}
}