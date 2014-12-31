<?php
/**
 * fewlines CMS
 *
 * Description: The query object which builds
 * query and holds information for the last
 * query to send to the server
 *
 * @copyright Copyright (c) fewlines
 * @author Davide Perozzi
 */

namespace Fewlines\Database\Select;

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
	 * The type of the query
	 *
	 * @var string
	 */
	private $type;

	/**
	 * The table to operate with
	 *
	 * @var string
	 */
	private $table;

	/**
	 * The columns to handle with
	 *
	 * @var array
	 */
	private $column;

	/**
	 * Where references
	 *
	 * @var array
	 */
	private $where;

	/**
	 * Limit of the results
	 *
	 * @var array
	 */
	private $limit = array();

	/**
	 * Values for update and insert
	 * function
	 *
	 * @var array
	 */
	private $values = array();

	/**
	 * Tells if the query is valid
	 *
	 * @var boolean
	 */
	private $isValid = true;

	/**
	 * Saves the query string builded
	 *
	 * @var string
	 */
	private $queryString = "";

	/**
	 * @param string $table
	 * @return \Fewlines\Database\Select\Query
	 */
	public function setTable($table)
	{
		$this->table = $table;
		return $this;
	}

	/**
	 * Defines the type of the query.
	 * Types: select, insert, update
	 *
	 * @param string $type
	 * @return \Fewlines\Database\Select\Query
	 */
	public function setType($type)
	{
		switch(strtolower($type))
		{
			case 'insert':
			case 'update':
			case 'select':
			case 'delete':
				// Nothing to change...
			break;

			default:
				$type = 'select';
			break;
		}

		$this->type = $type;

		return $this;
	}

	/**
	 * Sets the column(s)
	 *
	 * @param array $column
	 * @return \Fewlines\Database\Select\Query
	 */
	public function setColumn($column)
	{
		$this->column = $column;
		return $this;
	}

	/**
	 * Sets all where references
	 *
	 * @param array $where
	 * @return \Fewlines\Database\Select\Query
	 */
	public function setWhere($where)
	{
		$this->where = $where;
		return $this;
	}

	/**
	 * Sets the limit of the results
	 *
	 * @param array $limit
	 * @return \Fewlines\Database\Select\Query
	 */
	public function setLimit($limit)
	{
		$this->limit = $limit;
		return $this;
	}

	/**
	 * Sets the values
	 *
	 * @param array $values
	 * @return \Fewlines\Database\Select\Query
	 */
	public function setValues($values)
	{
		$this->values = $values;
		return $this;
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
	 * Sets the valid state of the query
	 *
	 * @param boolean $isValid
	 */
	private function setValid($isValid)
	{
		$this->isValid = $isValid;
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


	/**
	 * Gets the column transformed as string
	 *
	 * @return string
	 */
	public function getColumnString()
	{
		$columns = '';

		for($i = 0, $len = count($this->column); $i < $len; $i++)
		{
			$columns .= $this->column[$i]->getName();

			if($i != $len-1)
			{
				$columns .= ", ";
			}
		}

		return $columns;
	}

	/**
	 * Builds a query with the given information
	 *
	 * @return \Fewlines\Database\Select\Query
	 */
	public function build()
	{
		$this->clearString();

		switch($this->type)
		{
			case 'select':
				return $this->buildSelect();
			break;

			case 'insert':
				return $this->buildInsert();
			break;

			case 'update':
				return $this->buildUpdate();
			break;

			case 'delete':
				return $this->buildDelete();
			break;
		}
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
	 * Reset the query string
	 */
	public function clearString()
	{
		$this->queryString = "";
	}

	/**
	 * Adds all where conditions to the
	 * query string
	 */
	private function appendWhere()
	{
		$whereCount = count($this->where);

		for($i = 0; $i < $whereCount; $i++)
		{
			$where = $this->where[$i];

			if($i == 0)
			{
				$this->queryString .= self::WHERE;
			}

			$this->queryString .= $where->getCondition();

			if($i < $whereCount-1)
			{
				$this->queryString .= " " . $where->getOperator() . " ";
			}
		}
	}

	/**
	 * @return \Fewlines\Database\Select\Query
	 */
	private function buildSelect()
	{
		$this->queryString = self::SELECT;

		$columns           = $this->getColumnString();
		$columnCount       = count($this->column);

		$this->queryString .= $columns;
		$this->queryString .= self::FROM . self::quoteString($this->table);

		if($columnCount <= 0 || true == empty($this->column))
		{
			$this->setValid(false);
		}

		// Append where conditions (if given)
		$this->appendWhere();

		// Append limit (if given)
		if(false == empty($this->limit))
		{
			$this->queryString .= sprintf(self::LIMIT, $this->limit[0], $this->limit[1]);
		}

		return $this;
	}

	/**
	 * @return \Fewlines\Database\Select\Query
	 */
	private function buildInsert()
	{
		$valuesCount = count($this->values);

		$this->queryString  = self::INSERT;
		$this->queryString .= self::INTO;
		$this->queryString .= self::quoteString($this->table);

		if(false == empty($this->column))
		{
			$this->queryString .= self::bracketString($this->getColumnString());
		}

		$this->queryString .= self::VALUES;

		// Add values
		$values = '';

		for($i = 0; $i < $valuesCount; $i++)
		{
			$values .= $this->values[$i]->getContent();

			if($i < $valuesCount-1)
			{
				$values .= ", ";
			}
		}

		$this->queryString .= self::bracketString($values);

		return $this;
	}

	/**
	 * @return \Fewlines\Database\Select\Query
	 */
	private function buildUpdate()
	{
		$this->queryString  = self::UPDATE;
		$this->queryString .= self::quoteString($this->table);
		$this->queryString .= self::SET;

		$columnCount = count($this->column);
		$valuesCount = count($this->values);

		if($columnCount != $valuesCount || true == empty($this->column))
		{
			$this->setValid(false);

			throw new Exception\ParameterCountDoesNotMatchException("
				Please check the update parameters. The count does
				not match.
				Column count: \"" . $columnCount . "\".
				Values count: \"" . $valuesCount . "\".
			");
		}

		// Assign values to columns
		for($i = 0; $i < $columnCount; $i++)
		{
			$column = $this->column[$i]->getName();
			$value  = $this->values[$i]->getContent();

			$this->queryString .=  $column . ' = ' . $value;

			if($i < $columnCount-1)
			{
				$this->queryString .= ", ";
			}
		}

		// Append where conditions
		$this->appendWhere();

		return $this;
	}

	/**
	 * @return \Fewlines\Database\Select\Query
	 */
	private function buildDelete()
	{
		$this->queryString  = self::DELETE;
		$this->queryString .= self::FROM;
		$this->queryString .= self::quoteString($this->table);

		$this->appendWhere();

		return $this;
	}
}

?>