<?php

namespace Fewlines\Component\Database;

class Query
{
	/**
	 * @var string
	 */
	const SELECT = "SELECT ";

	/**
	 * @var string
	 */
	const UPDATE = "UPDATE ";

	/**
	 * @var string
	 */
	const INSERT = "INSERT ";

	/**
	 * @var string
	 */
	const DROP = " DROP ";

	/**
	 * @var string
	 */
	const DELETE = "DELETE ";

	/**
	 * @var string
	 */
	const INTO = " INTO ";

	/**
	 * @var string
	 */
	const VALUES = " VALUES ";

	/**
	 * @var string
	 */
	const FROM = " FROM ";

	/**
	 * @var string
	 */
	const WHERE = " WHERE ";

	/**
	 * @var string
	 */
	const QUOTE = " `%s` ";

	/**
	 * @var string
	 */
	const BRACKET = " (%s) ";

	/**
	 * @var string
	 */
	const LIMIT = " LIMIT %s, %s ";

	/**
	 * @var string
	 */
	const SET = " SET ";

	/**
	 * @var string
	 */
	const TRUNCATE = " TRUNCATE ";

	/**
	 * @var string
	 */
	const TABLE = " TABLE ";

	/**
	 * @var string
	 */
	const CREATE = " CREATE ";

	/**
	 * @var string
	 */
	const NOT_NULL = " NOT NULL ";

	/**
	 * @var string
	 */
	const PRIMARY_KEY = " PRIMARY KEY ";

	/**
	 * @var string
	 */
	const UNIQUE = " UNIQUE ";

	/**
	 * @var string
	 */
	const AUTO_INCREMENT = " AUTO_INCREMENT ";

	/**
	 * @var string
	 */
	const COLLATE = " COLLATE ";

	/**
	 * Tells if the query is valid
	 *
	 * @var boolean
	 */
	protected $isValid = true;

	/**
	 * Saves the query string builded
	 *
	 * @var string
	 */
	protected $queryString = "";

	/**
	 * The type of the query
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * The table to operate with
	 *
	 * @var string
	 */
	protected $table;

	/**
	 * The columns to handle with
	 *
	 * @var array
	 */
	protected $column;

	/**
	 * Return the query string if
	 * the object is used as string
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->getString();
	}

	/**
	 * Gets the query as string to
	 * execute
	 *
	 * @return string
	 */
	public function getString()
	{
		return $this->queryString;
	}

	/**
	 * Reset the query string
	 */
	public function clearString()
	{
		$this->queryString = "";
	}

	/**
	 * Sets the valid state of the query
	 *
	 * @param boolean $isValid
	 */
	protected function setValid($isValid)
	{
		$this->isValid = $isValid;
	}

	/**
	 * Checks if the query is valid
	 *
	 * @return boolean
	 */
	public function isValid()
	{
		return $this->isValid;
	}

	/**
	 * Defines the type of the query.
	 * Types: select, insert, update
	 *
	 * @param string $type
	 * @return \Fewlines\Component\Database\Query|\Fewlines\Component\Database\Select\Query
	 */
	public function setType($type)
	{
		$this->type = $type;
		return $this;
	}

	/**
	 * @param string $table
	 * @return \Fewlines\Component\Database\Query|\Fewlines\Component\Database\Select\Query
	 */
	public function setTable($table)
	{
		$this->table = $table;
		return $this;
	}

	/**
	 * Sets the column(s)
	 *
	 * @param array $column
	 * @return \Fewlines\Component\Database\Query|\Fewlines\Component\Database\Select\Query
	 */
	public function setColumn($column)
	{
		$this->column = $column;
		return $this;
	}

	/**
	 * Quotes a string
	 *
	 * @param  string $str
	 * @return string
	 */
	public static function quoteString($str)
	{
		return sprintf(self::QUOTE, $str);
	}

	/**
	 * Brackets a string
	 *
	 * @param  string $str
	 * @return string
	 */
	public static function bracketString($str)
	{
		return sprintf(self::BRACKET, $str);
	}
}