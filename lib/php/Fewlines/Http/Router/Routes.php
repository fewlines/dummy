<?php
namespace Fewlines\Http\Router;

class Routes
{
	/**
	 * @var array
	 */
	protected $routes = array();

	/**
	 * Add a route manually
	 *
	 * @param string $type
	 * @param string $from
	 * @param string $to
	 */
	public function addRoute($type, $from, $to) {
		$this->routes[] = new Routes\Route($type, $from, $to);
	}

	/**
	 * Returns all routes defined
	 * by the user in the config
	 *
	 * @return array
	 */
    public function getRoutes() {
    	return $this->routes;
    }
}
