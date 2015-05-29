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
	 * @param string $name
	 */
	public static function get($name) {
		if (array_key_exists($name, self::$values)) {
			return self::$values[$name];
		}

		throw new Registry\Exception\PropertyNotFoundException(
			'The property "' . $name . '" does not exist in this registry.'
		);
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