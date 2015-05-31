<?php

namespace Fewlines\Component\Database\Table;

class Column
{
	/**
	 * Name of the column
	 *
	 * @var string
	 */
	private $name;

	/**
	 * Type of the column
	 *
	 * @var string
	 */
	private $type;

	/**
	 * Length of the column (type)
	 *
	 * @var integer
	 */
	private $length;

	/**
	 * Auto increment
	 *
	 * @var boolean
	 */
	private $autoIncrement;

	/**
	 * check if not null is set
	 *
	 * @var boolean
	 */
	private $notNull;

	/**
	 * Index of the column
	 *
	 * @var string
	 */
	private $index;

	/**
	 * The collation of the column
	 *
	 * @var string
	 */
	private $collate;

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @param string $type
	 */
	public function setType($type)
	{
		$this->type = $type;
	}

	/**
	 * @param integer $length
	 */
	public function setLength($length)
	{
		$this->length = $length;
	}

	/**
	 * @param boolean $autoIncrement
	 */
	public function setAutoIncrement($autoIncrement)
	{
		$this->autoIncrement = $autoIncrement;
	}

	/**
	 * @param boolean $notNull
	 */
	public function setNotNull($notNull)
	{
		$this->notNull = $notNull;
	}

	/**
	 * @param string $index
	 */
	public function setIndex($index)
	{
		$this->index = $index;
	}

	/**
	 * @param string $collate
	 */
	public function setCollate($collate)
	{
		$this->collate = $collate;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @return integer
	 */
	public function getLength()
	{
		return $this->length;
	}

	/**
	 * @return boolean
	 */
	public function getAutoIncrement()
	{
		return $this->autoIncrement;
	}

	/**
	 * @return boolean
	 */
	public function getNotNull()
	{
		return $this->notNull;
	}

	/**
	 * @return string
	 */
	public function getIndex()
	{
		return $this->index;
	}

	/**
	 * @return string
	 */
	public function getCollate()
	{
		return $this->collate;
	}
}