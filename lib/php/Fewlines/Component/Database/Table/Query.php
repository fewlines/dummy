<?php

namespace Fewlines\Component\Database\Table;

class Query extends \Fewlines\Component\Database\Query
{
	/**
	 * Table object with columns and options
	 *
	 * @var \Fewlines\Component\Database\Table\Table
	 */
	protected $table;

	/**
	 * Build the query string by type
	 *
	 * @return \Fewlines\Component\Database\Table\Query
	 */
	public function build()
	{
		$this->clearString();

		switch($this->type)
		{
			case 'create':
				return $this->buildCreate();
			break;
		}
	}

	/**
	 * @return \Fewlines\Component\Database\Table\Query
	 */
	private function buildCreate()
	{
		$name         = $this->table->getName();
		$columns      = $this->table->getColumns();
		$tableCollate = $this->table->getCollate();

		$this->queryString  = self::CREATE;
		$this->queryString .= self::TABLE;
		$this->queryString .= self::quoteString($name);

		$columnString = '';

		for($i = 0, $len = count($columns); $i < $len; $i++)
		{
			// Set main options
			$column        = $columns[$i];
			$columnString .= $column->getName();
			$columnString .= " " . $column->getType();

			// Set length of column
			if(false == is_null($column->getLength()))
			{
				$columnString .= trim(self::bracketString($column->getLength()));
			}

			// Set not null option
			if(false == is_null($column->getNotNull()) &&
				true == $column->getNotNull())
			{
				$columnString .= rtrim(self::NOT_NULL);
			}

			// Set index
			if(false == is_null($column->getIndex()))
			{
				switch(strtolower($column->getIndex()))
				{
					case 'primary':
						$columnString .= rtrim(self::PRIMARY_KEY);
					break;

					case 'unique':
						$columnString .= rtrim(self::UNIQUE);
					break;
				}
			}

			// Set auto increment
			if(false == is_null($column->getAutoIncrement()) &&
				true == $column->getAutoIncrement())
			{
				$columnString .= rtrim(self::AUTO_INCREMENT);
			}

			// Set collate
			if(false == is_null($column->getCollate()))
			{
				$columnString .= self::COLLATE . $column->getCollate();
			}

			if($i < $len-1)
			{
				$columnString .= ", \r\n";
			}
		}

		$columnString = self::bracketString($columnString);

		// Combine stmt and column stmt
		$this->queryString .= $columnString;

		// Set collate of the table
		if(false == is_null($tableCollate))
		{
			$this->queryString .= self::COLLATE . $tableCollate;
		}

		return $this;
	}
}