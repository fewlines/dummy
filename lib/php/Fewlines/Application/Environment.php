<?php
namespace Fewlines\Application;

class Environment
{
	/**
	 * @var string
	 */
	private static $env;

	/**
	 * @var array
	 */
	private static $locals = array(
			'development', 'testing', 'test', 'local', '127.0.0.1', 'localhost'
		);

	/**
	 * Returns the current
	 * environment
	 *
	 * @return string
	 */
	public static function get() {
		return self::$env;
	}

	/**
	 * @param string $env
	 */
	public static function set($env) {
		self::$env = $env;
	}

	/**
	 * @param string $name
	 */
	public static function addLocal($name) {
		self::$locals[] = $name;
	}

	/**
	 * @return boolean
	 */
	public static function isLocal() {
		return in_array(self::$env, self::$locals);
	}
}