<?php

namespace Fewlines\Component\Database\Select;

class Value
{
	/**
	 * The content of this value
	 *
	 * @var string
	 */
	private $content;

	/**
	 * @param string $content
	 */
	public function __construct($content)
	{
		$this->content = $content;
	}

	/**
	 * Get the content of a value
	 *
	 * @return string
	 */
	public function getContent()
	{
		return $this->content;
	}
}