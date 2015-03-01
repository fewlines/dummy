<?php

namespace Fewlines\Dom;

class Dom
{
	/**
	 * @var null|\Fewlines\Dom\Dom
	 */
	private static $instance;

	/**
	 * @return \Fewlines\Dom\Dom
	 */
	public static function getInstance()
	{
		if(self::$instance instanceof \Fewlines\Dom\Dom)
		{
			return self::$instance;
		}

		return new self();
	}

	/**
	 * @param  string $tag
	 * @param  array  $attributes
	 * @param  string $content
	 * @return \Fewlines\Dom\Element
	 */
	public function createElement($tag, $attributes = array(), $content = "", $children = array())
	{
		$element = new Element;

		$element->setAttributes($attributes);
		$element->setContent($content);
		$element->setChildren($children);

		switch(strtolower($tag))
		{
			default:
			case Element::DIV_TAG:
				$element->setDomTag(Element::DIV_TAG);
				$element->setDomStr(Element::DIV_STR);
			break;

			case Element::SPAN_TAG:
				$element->setDomTag(Element::SPAN_TAG);
				$element->setDomStr(Element::SPAN_STR);
			break;

			case Element::META_TAG:
				$element->setDomTag(Element::META_TAG);
				$element->setDomStr(Element::META_STR);
			break;

			case Element::INPUT_TAG:
				$element->setDomTag(Element::INPUT_TAG);
				$element->setDomStr(Element::INPUT_STR);
			break;

			case Element::FORM_TAG:
				$element->setDomTag(Element::FORM_TAG);
				$element->setDomStr(Element::FORM_STR);
			break;
		}

		return $element;
	}
}