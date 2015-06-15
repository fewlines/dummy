<?php

namespace Fewlines\Component\Database\Select;

class Column
{
	/**
	 * The name of the column
	 *
	 * @var string
	 */
	private $name;

	/**
	 * @param string $name
	 */
	public function __construct($name)
	{
		$this->name = $name;
	}

	/**
	 * Get the name of the column
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}
}