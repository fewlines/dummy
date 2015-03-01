<?php

namespace Fewlines\Dom;

class Element extends Element\Renderer
{
	/**
	 * @var string
	 */
	const DIV_TAG = 'div';

	/**
	 * @var string
	 */
	const DIV_STR = '<div %s>%s</div>';

	/**
	 * @var string
	 */
	const INPUT_TAG = 'input';

	/**
	 * @var string
	 */
	const INPUT_STR = '<input %s/>';

	/**
	 * @var string
	 */
	const SPAN_TAG = 'span';

	/**
	 * @var string
	 */
	const SPAN_STR = '<span %s>%s</span>';

	/**
	 * @var string
	 */
	const META_TAG = 'meta';

	/**
	 * @var string
	 */
	const META_STR = '<meta %s/>';

	/**
	 * @var string
	 */
	const FORM_TAG = 'form';

	/**
	 * @var string
	 */
	const FORM_STR = '<form %s>%s</form>';

	/**
	 * @var string
	 */
	const SELECT_TAG = 'select';

	/**
	 * @var string
	 */
	const SELECT_STR = '<select %s>%s</select>';

	/**
	 * @var string
	 */
	const TEXTAREA_TAG = 'textarea';

	/**
	 * @var string
	 */
	const TEXTAREA_STR = '<textarea %s>%s</textarea>';

	/**
	 * @var string
	 */
	private $content = '';

	/**
	 * @var array
	 */
	private $attributes = array();

	/**
	 * @var string
	 */
	private $domTag;

	/**
	 * @var string
	 */
	private $domStr;

	/**
	 * @var array
	 */
	private $children = array();

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
	 * @param array $children
	 */
	public function setChildren($children)
	{
		$this->children = $children;
	}

	/**
	 * @param string $str
	 */
	public function setDomStr($str)
	{
		$this->domStr = $str;
	}

	/**
	 * @param string $tag
	 */
	public function setDomTag($tag)
	{
		$this->domTag = $tag;
	}

	/**
	 * @return string
	 */
	public function getTag()
	{
		return $this->domTag;
	}

	public function setAttributes($attributes)
	{
		if(false == is_array($attributes))
		{
			throw new Exception\InvalidElementAttributesTypeException("
				The attributes given has an invlid type.
				Excepting array.
			");
		}

		$this->attributes = $attributes;
	}

	/**
	 * @param  string $name
	 * @return string
	 */
	public function getAttribute($name)
	{
		if(true == array_key_exists($name, $this->attributes))
		{
			return (string) $this->attributes[$name];
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
	 * @return string
	 */
	public function render()
	{
		$content = $this->content;

		// Add child elements to content
		for($i = 0, $len = count($this->children); $i < $len; $i++)
		{
			$content .= $this->children[$i]->render();
		}

		return $this->renderStr($this->str, $this->getAttributeString(), $content);
	}

	/**
	 * @param  array $attributes
	 * @return string
	 */
	private function getAttributeString()
	{
		$attrStr = '';

		foreach($this->attributes as $name => $content)
		{
			$attrStr .= $name . '="' . $content . '" ';
		}

		return $attrStr;
	}
}