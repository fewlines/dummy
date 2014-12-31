<?php
/**
 * fewlines CMS
 *
 * Description: Holds information
 * about a connection between the
 * application and the database
 *
 * @copyright Copyright (c) fewlines
 * @author Davide Perozzi
 */

namespace Fewlines\Database;

class Link
{
	/**
	 * The link of the database connection
	 *
	 * @var \Mysqli
	 */
	private $link;

	/**
	 * The host address of the database
	 *
	 * @var string
	 */
	private $host;

	/**
	 * The username of the connection
	 *
	 * @var string
	 */
	private $user;

	/**
	 * The password for the user
	 *
	 * @var string
	 */
	private $password;

	/**
	 * The name of the database selected
	 *
	 * @var string
	 */
	private $database;

	/**
	 * The port the server uses
	 *
	 * @var string
	 */
	private $port;

	/**
	 * Create the link
	 *
	 * @param string  $host
	 * @param string  $user
	 * @param string  $password
	 * @param string  $database
	 * @param integer $port
	 */
	public function __construct($host, $user, $password, $database, $port)
	{
		$this->host     = $host;
		$this->user     = $user;
		$this->password = $password;
		$this->database = $database;
		$this->port     = $port;

		// Create link
		$this->link = new \MySqli($host, $user, $password, $database, $port);
	}

	/**
	 * Calls a given function on the conection
	 * object. Acts as a "proxy".
	 *
	 * @param  string $name
	 * @param  array  $parameters
	 * @return *
	 */
	public function __call($name, $parameters)
	{
		return call_user_func_array(array($this->link, $name), $parameters);
	}

	/**
	 * Gets a property of the mysqli link
	 *
	 * @param  string $property
	 * @return *
	 */
	public function __get($property)
	{
		return $this->link->$property;
	}
}

?>