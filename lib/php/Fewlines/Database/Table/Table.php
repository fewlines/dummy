<?php
/**
 * fewlines CMS
 *
 * Description: Table object
 *
 * @copyright Copyright (c) fewlines
 * @author Davide Perozzi
 */

namespace Fewlines\Database\Table;

class Table
{
	/**
	 * The name of the table
	 *
	 * @var string
	 */
	private $name;

	/**
	 * The columns of the table
	 *
	 * @var array
	 */
	private $columns = array();

	/**
	 * Collate for the table
	 *
	 * @var string
	 */
	private $collate = "utf8_unicode_ci";

	/**
	 * Sets the name of the table
	 *
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * Sets the collate of the table
	 *
	 * @param string $collate
	 */
	public function setCollate($collate)
	{
		$this->collate = $collate;
	}

	/**
	 * Creates the column from a given array
	 *
	 * @param array $columns
	 */
	public function setColumns($columns)
	{
		if(false == is_array($columns))
		{
			return;
		}

		foreach($columns as $name => $options)
		{
			$column = new Column;

			$type          = $this->getOptionByKey("type", $options);
			$length        = $this->getOptionByKey("length", $options);
			$notNull       = $this->getOptionByKey("notNull", $options);
			$autoIncrement = $this->getOptionByKey("autoIncrement", $options);
			$index         = $this->getOptionByKey("index", $options);
			$collate       = $this->getOptionByKey("collate", $options);

			$column->setName($name);

			// Set all options if set
			if(false != $type)
			{
				$column->setType($type);
			}

			if(false != $length)
			{
				$column->setLength($length);
			}

			if(false != $notNull)
			{
				$column->setNotNull($notNull);
			}

			if(false != $autoIncrement)
			{
				$column->setAutoIncrement($autoIncrement);
			}

			if(false != $index)
			{
				$column->setIndex($index);
			}

			if(false != $collate)
			{
				$column->setCollate($collate);
			}

			// Add column
			$this->columns[] = $column;
		}

	}

	/**
	 * Returns the name of the table
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Returns the created columns
	 *
	 * @return array
	 */
	public function getColumns()
	{
		return $this->columns;
	}

	/**
	 * Returns the collate
	 *
	 * @return array
	 */
	public function getCollate()
	{
		return $this->collate;
	}

	/**
	 * Gets a option by key
	 *
	 * @param  string  $key
	 * @param  integer $options
	 * @return string|number|boolean
	 */
	private function getOptionByKey($key, $options)
	{
		return array_key_exists($key, $options) ? $options[$key] : false;
	}
}

?>