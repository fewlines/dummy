<?php
/**
 * fewlines CMS
 *
 * Description: Column reference
 *
 * @copyright Copyright (c) fewlines
 * @author Davide Perozzi
 */

namespace Fewlines\Database\Select;

class Column
{
	/**
	 * The name of the column
	 *
	 * @var string
	 */
	private $name;

	/**
	 * @param string $content
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

?>