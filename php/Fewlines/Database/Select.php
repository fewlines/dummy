<?php
/**
 * fewlines CMS
 *
 * Description: This class controls
 * the connection between the
 * application and the database
 *
 * @copyright Copyright (c) fewlines
 * @author Davide Perozzi
 */

namespace Fewlines\Database;

class Select
{
	/**
	 * The Database instance
	 * for a refernce of the link
	 *
	 * @var \Fewlines\Database\Database
	 */
	private $database;

	/**
	 * The name of the table selected
	 *
	 * @var string
	 */
	private $table;

	/**
	 * The columns which to select
	 *
	 * @var array|string
	 */
	private $column;

	/**
	 * Tells what to select
	 *
	 * @var \Fewlines\Database\Select\Where
	 */
	private $where;

	/**
	 * The limit of the
	 * query
	 *
	 * @var array
	 */
	private $limit = array();

	/**
	 * Values to handle updates and inserts
	 *
	 * @var array
	 */
	private $values = array();

	/**
	 * The query object
	 *
	 * @var \Fewlines\Database\Select\Query
	 */
	private $query;

	/**
	 * Tells if the current object is a update
	 * query
	 *
	 * @var boolean
	 */
	private $isUpdate = false;

	/**
	 * Tells if the current object is a insert
	 * query
	 *
	 * @var boolean
	 */
	private $isInsert = false;

	/**
	 * Tells if the current object is a insert
	 * query
	 *
	 * @var boolean
	 */
	private $isDelete = false;

	/**
	 * @param string 				      $table
	 * @param array|string                $column
	 * @param \Fewlines\Database\Database $database
	 */
	public function __construct($table, $column, $database)
	{
		$this->setTable($table);
		$this->setColumn($column);
		$this->database = $database;
	}

	/**
	 * Sets the table manually
	 *
	 * @param string $table
	 */
	public function setTable($table)
	{
		$this->table = $table;
	}

	/**
	 * Set the column manually
	 *
	 * @param array|string $column
	 */
	public function setColumn($column)
	{
		if(true == empty($column))
		{
			return;
		}

		// Force array
		$columns = is_array($column) ? $column : array($column);

		// Create column references
		foreach($columns as $column)
		{
			$this->column[] = new Select\Column($column);
		}
	}

	/**
	 * Returns the table selected
	 *
	 * @return string
	 */
	public function getTable()
	{
		return $table;
	}

	/**
	 * Adds a condition
	 *
	 * @param  array $condition
	 * @return \Fewlines\Database\Select
	 */
	public function where($condition, $operator = "AND")
	{
		// Escape strings
		if(array_key_exists(2, $condition))
		{
			$condition[2] = "'" . $this->database->realEscapeString($condition[2]) . "'";
		}

		$this->where[] = new Select\Where($condition, $operator);
		return $this;
	}

	/**
	 * Sets a limit of the results
	 *
	 * @param  integer $start
	 * @param  integer $count
	 * @return \Fewlines\Database\Select
	 */
	public function limit($start, $count)
	{
		$this->limit = array($start, $count);
		return $this;
	}

	/**
	 * Fetch selected
	 *
	 * @param  boolean $oneRow
	 * @return array
	 */
	public function fetchAll($oneRow = false)
	{
		$query       = $this->getQuerySelect();
		$result      = $this->database->query($query);
		$resultArray = array();

		while($row = $result->fetch_object())
		{
			$resultArray[] = (array) $row;

			if(true == $oneRow)
			{
				$resultArray = $resultArray[0];
				break;
			}
		}

		return $resultArray;
	}

	/**
	 * Fetch one result (the first)
	 *
	 * @return array
	 */
	public function fetchRow()
	{
		return $this->fetchAll(true);
	}

	/**
	 * Set the values for insert and update
	 * functions
	 *
	 * @param array $values
	 */
	private function setValues($values)
	{
		// Forece array
		$values = is_array($values) ? $values : array($values);

		// Create values
		foreach($values as $value)
		{
			$content        = "'" . $this->database->realEscapeString($value) . "'";
			$this->values[] = new Select\Value($content);
		}
	}

	/**
	 * Updates the selected database given
	 * with columns
	 *
	 * @param array $values
	 * @return \Fewlines\Database\Select
	 */
	public function update($values)
	{
		$this->isUpdate = true;
		$this->setValues($values);

		return $this;
	}

	/**
	 * Insert ner records into the selected
	 * database
	 *
	 * @param array $values
	 * @return \Fewlines\Database\Select
	 */
	public function insert($values)
	{
		$this->isInsert = true;
		$this->setValues($values);

		return $this;
	}

	/**
	 * Deletes records
	 *
	 * @return \Fewlines\Database\Select
	 */
	public function delete()
	{
		$this->isDelete = true;
		return $this;
	}

	/**
	 * Executes the build up query
	 *
	 * @return boolean
	 */
	public function execute()
	{
		// Detect type
		if(true == $this->isUpdate)
		{
			$query = $this->getQueryUpdate();
		}
		else if(true == $this->isInsert)
		{
			$query = $this->getQueryInsert();
		}
		else if(true == $this->isDelete)
		{
			$query = $this->getQueryDelete();
		}

		// Execute query
		$result = $this->database->query($query);

		// Validating result
		if(true == $result)
		{
			return $result;
		}

		return false;
	}

	/**
	 * Check if the query is valid
	 *
	 * @param  \Fewlines\Database\Select\Query $query
	 * @return boolean
	 */
	private function checkQuery($query)
	{
		if(false == $query->isValid())
		{
			throw new Exception\SelectQueryInvalidException("
				You are trying to execute a invalid query.
				Please fix it to prevent this exception.
				Error: \"" . $this->database->getError() . "\"
			");
		}

		return $query->isValid();
	}

	/**
	 * Builds the query for the select
	 * operations
	 *
	 * @return \Fewlines\Database\Select\Query
	 */
	public function getQuerySelect()
	{
		$query = new Select\Query;

		$query->setType("select")
			  ->setTable($this->table)
			  ->setColumn($this->column)
			  ->setWhere($this->where)
			  ->setLimit($this->limit);

		$query = $query->build();
		$this->checkQuery($query);

		return $query;
	}

	/**
	 * Builds the query for the select
	 * operations
	 *
	 * @return \Fewlines\Database\Select\Query
	 */
	public function getQueryInsert()
	{
		$query = new Select\Query;

		$query->setType("insert")
			  ->setTable($this->table)
			  ->setColumn($this->column)
			  ->setWhere($this->where)
			  ->setValues($this->values);

		$query = $query->build();
		$this->checkQuery($query);

		return $query;
	}

	/**
	 * Builds the query for the select
	 * operations
	 *
	 * @return \Fewlines\Database\Select\Query
	 */
	public function getQueryUpdate()
	{
		$query = new Select\Query;

		$query->setType("update")
			  ->setTable($this->table)
			  ->setColumn($this->column)
			  ->setWhere($this->where)
			  ->setValues($this->values);

		$query = $query->build();
		$this->checkQuery($query);

		return $query;
	}

	/**
	 * Builds the query for the delete
	 * operation
	 *
	 * @return \Fewlines\Database\Select\Query
	 */
	public function getQueryDelete()
	{
		$query = new Select\Query;

		$query->setType("delete")
			  ->setTable($this->table)
			  ->setWhere($this->where);

		$query = $query->build();
		$this->checkQuery($query);

		return $query;
	}
}

?>