<?php

namespace Fewlines\Database\Select;

class Query extends \Fewlines\Database\Query
{
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
				$this->buildSelect();
			break;

			case 'insert':
				$this->buildInsert();
			break;

			case 'update':
				$this->buildUpdate();
			break;

			case 'delete':
				$this->buildDelete();
			break;

			case 'truncate':
				$this->buildTruncate();
			break;

			case 'drop':
				$this->buildDrop();
			break;
		}

		// Append ending semicolon
		$this->queryString .= ";";

		return $this;
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

		// Add columns from value
		$columnNames   = '';
		$values        = '';
		$columnCounter = 0;

		foreach($this->values as $name => $value)
		{
			$columnNames .= $name;
			$values      .= $value->getContent();

			if($columnCounter < $valuesCount-1)
			{
				$columnNames .= ", ";
				$values      .= ", ";
			}

			$columnCounter++;
		}

		$this->queryString .= self::bracketString($columnNames);
		$this->queryString .= self::VALUES;
		$this->queryString .= self::bracketString($values);

		pr($this->queryString);

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

		$valuesCount = count($this->values);

		// Assign values to columns
		$counter = 0;
		foreach($this->values as $name => $value)
		{
			$this->queryString .=  $name . ' = ' . $value->getContent();

			if($counter < $valuesCount-1)
			{
				$this->queryString .= ", ";
			}

			$counter++;
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
		$this->queryString = self::DELETE;

		if(count($this->column) > 0)
		{
			$this->queryString .= $this->getColumnString();
		}

		$this->queryString .= self::FROM;
		$this->queryString .= self::quoteString($this->table);

		$this->appendWhere();

		return $this;
	}

	/**
	 * @return \Fewlines\Database\Select\Query
	 */
	private function buildTruncate()
	{
		$this->queryString  = self::TRUNCATE;
		$this->queryString .= self::TABLE;
		$this->queryString .= self::quoteString($this->table);

		return $this;
	}

	/**
	 * @return \Fewlines\Database\Select\Query
	 */
	private function buildDrop()
	{
		$this->queryString  = self::DROP;
		$this->queryString .= self::TABLE;
		$this->queryString .= self::quoteString($this->table);

		return $this;
	}
}