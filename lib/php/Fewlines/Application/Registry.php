<?php
namespace Fewlines\Application;

class Registry
{
	/**
	 * Holds all definitions
	 *
	 * @var array
	 */
	private static $values = array();

	/**
	 * A global function to get a
	 * property from the registry
	 * which are private
	 *
	 * @param  string $name
	 * @return *
	 */
	public static function get($name) {
		if (array_key_exists($name, self::$values)) {
			return self::$values[$name];
		}

		return null;
	}

	/**
	 * Sets a new property to the
	 * registry
	 *
	 * @param string $name
	 * @param * $value
	 */
	public static function set($name, $value) {
		if (array_key_exists($name, self::$values)) {
			throw new Registry\Exception\PropertyExistsException(
				'The property "' . $name . '" already exists and can\'t be overwritten'
			);
		}

		self::$values[$name] = $value;
	}
}