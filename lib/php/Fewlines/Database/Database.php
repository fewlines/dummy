<?php

namespace Fewlines\Database;

use Fewlines\Application\Config;

class Database
{
	/**
	 * Tells if the strings should be
	 * quoted to prevent sql injection
	 *
	 * @var boolean
	 */
	const REAL_ESCAPE = true;

	/**
	 * The link of the database connection
	 *
	 * @var \Fewlines\Database\Link
	 */
	private $link;

	/**
	 * The select object with the
	 * table name etc.
	 *
	 * @var string
	 */
	private $select;

	/**
	 * Simple database constructor which inits
	 * the required information for a database
	 * connection
	 *
	 * @param string $host
	 * @param string $user
	 * @param string $password
	 * @param string $database
	 */
	public function __construct($host = "default", $user = "", $password = "", $database = "")
	{
		$defaultPort = ini_get("mysqli.default_port");

		if($host == "default")
		{
			// Get information from the config
			// of the application
			$config = Config::getInstance();
			$dbCfg  = $config->getElementByPath('database');

			$host     = $dbCfg->getChildByName('host')->getContent();
			$user     = $dbCfg->getChildByName('user')->getContent();
			$password = $dbCfg->getChildByName('password')->getContent();
			$database = $dbCfg->getChildByName('database')->getContent();
			$port     = $dbCfg->getChildByName('port');
			$port     = false == $port ? $defaultPort : $port->getContent();
		}
		else
		{
			// Get port from host (if set)
			$port = end(split(":", $host));
			$port = $port != $host ? $port : $defaultPort;
			$host = $port != $host ? reset(split(":", $host)) : $host;
		}

		// Create link for connection
		$this->link = new Link($host, $user, $password, $database, $port);
	}

	/**
	 * Selects or changes the current database
	 *
	 * @param  string $database
	 * @return boolean
	 */
	public function selectDatabase($database)
	{
		$state = $this->link->select_db($database);

		if(false == $state)
		{
			throw new Exception\DatabaseNotFoundException(
				"The database \"" . $database . "\" could
				not be selected. Maybe it doesn't exist."
			);
		}

		return $state;
	}

	/**
	 * Selects a table with the select
	 * reference
	 *
	 * @param  string $table
	 * @param  string $column
	 * @return \Fewlines\Database\Database
	 */
	public function select($table, $column = "")
	{
		$this->select = new Select($table, $column, $this);
		return $this->select;
	}

	/**
	 * Create a new table in the connected database
	 *
	 * @param  string $tablename
	 * @param  arra   $columns
	 * @return boolean
	 */
	public function createTable($tablename, $columns)
	{
		$table = new Table\Table;
		$query = new Table\Query;

		$table->setName($tablename);
		$table->setColumns($columns);

		$query->setType("create")
			  ->setTable($table);

		$query = $query->build();

		// Execute query and return result
		return $this->query($query);
	}

	/**
	 * Quotes a string (query)
	 *
	 * @param  string $str
	 * @return string
	 */
	public function realEscapeString($str)
	{
		if(true == self::REAL_ESCAPE)
		{
			return $this->link->escape_string($str);
		}

		return $str;
	}

	/**
	 * Execute a raw query
	 *
	 * @param  string $query
	 * @return \mysqli_result
	 */
	public function query($query)
	{
		$result = $this->link->query($query);

		if(false == $result)
		{
			throw new Exception\SelectResultInvalidException("
				There is something wrong with the result of
				your query.
				Error: " . $this->getError() . ".
				Query: " . $query . ".
			");
		}

		return $result;
	}

	/**
	 * Prepare a query
	 *
	 * @param  string $query
	 * @return \mysqli_statement
	 */
	public function prepare($query)
	{
		return $this->link->prepare($query);
	}

	/**
	 * Returns error state
	 *
	 * @return string
	 */
	public function getError()
	{
		return $this->link->error;
	}
}