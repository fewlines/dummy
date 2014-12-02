<?php
/**
 * fewlines CMS
 *
 * Description: Holds all requests that affect the
 * url
 *
 * @copyright Copyright (c) fewlines
 * @author Davide Perozzi
 */

namespace Fewlines\Http;

use Fewlines\Http\Router as Router;

class Request extends Router
{
	/**
	 * Holds the instace for singleton getter
	 *
	 * @var Fewlines\Http\Request
	 */
	private static $instance;

	public function __construct()
	{
		$this->initRouter($_SERVER['REQUEST_URI']);
		self::$instance = $this;
	}

	/**
	 * Get the instance
	 *
	 * @return
	 */
	public static function getInstance()
	{
		return self::$instance;
	}

	/**
	 * Returns the action route url layout
	 *
	 * @return string
	 */
	public function getUrlLayoutRouter()
	{
		return $this->getUrlLayout();
	}

	/**
	 * Returns all parameters given
	 * by the router (get or post)
	 *
	 * @param  string $type
	 * @return array
	 */
	public function getParams($type = 'get')
	{
		$type = strtolower($type);
		$parameters = array();

		switch($type)
		{
			case 'post':
				$parameters = $this->getUrlPartsPOST();
			break;

			default:
			case 'get':
				$parameters = $this->getUrlPartsGET();
			break;
		}

		return $parameters;
	}

	/**
	 * Returns a parameters value
	 * by the given type and name
	 *
	 * @param string $name
	 * @param string $type
	 * @return *
	 */
	public function getParam($name, $type = 'get')
	{
		$type = strtolower($type);
		$parameters = $this->getParams($type);

		return array_key_exists($name, $parameters)
				? $parameters[$name]
				: NULL;
	}

	/**
	 * Returns all methods of the route
	 * with content
	 *
	 * @return array
	 */
	public function getUrlMethodContents()
	{
		return $this->getRouteUrlParts($this->getUrlLayoutRouter());
	}
}

?>