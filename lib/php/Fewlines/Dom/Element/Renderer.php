<?php

namespace Fewlines\Dom\Element;

abstract class Renderer
{
	/**
	 * @param  string $parseStr
	 * @param  string $attributeStr
	 * @param  string $content
	 * @return string
	 */
	protected function renderStr($parseStr, $attributeStr, $content)
	{
		return sprintf($parseStr, $attributeStr, $content);
	}
}