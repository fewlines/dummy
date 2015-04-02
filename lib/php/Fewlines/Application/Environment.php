<?php
namespace Fewlines\Application;

class Environment
{
	/**
	 * @var string
	 */
	private static $env;

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
	 * @return boolean
	 */
	public static function isLocal() {
		return self::$env == 'development' || self::$env == 'local' || self::$env == '127.0.0.1';
	}
}