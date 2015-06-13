<?php
namespace Fewlines\Core\Helper;

use Fewlines\Core\Locale\Locale;

class ShortcutHelper
{
	/**
	 * @var string
	 */
	const SHORTCUT_IDENTIFIER_PATTERN = '/^:(.*):\{(.*)\}$/';

	/**
	 * @var string
	 */
	const SHORTCUT_FUNCTION_PREFIX = 'exec';

	/**
	 * Parse a string to get the string
	 * with executed functions
	 *
	 * @param  string $str
	 * @return string
	 */
	public static function parse($str) {
		$name = strtolower(self::getStringVars($str, 0));
		$value = self::getStringVars($str, 1);

		return self::executeShortcut($name, $value);
	}

	/**
	 * Executes the shortcut function and
	 * returns it value (must be a string)
	 *
	 * @param  string $name
	 * @param  string $value
	 * @return string
	 */
	public static function executeShortcut($name, $value) {
		$method = self::SHORTCUT_FUNCTION_PREFIX . strtoupper($name);
		$class = '\\' . __CLASS__;
		$return = '';

		if (method_exists($class, $method)) {
			$return = call_user_func_array($class . '::' . $method, array($value));
		}

		return $return;
	}

	/**
	 * Gets the variable from the match
	 * of the pattern and the string
	 *
	 * @param  string $str
	 * @param  integer $index
	 * @return string
	 */
	private static function getStringVars($str, $index) {
		preg_match(self::SHORTCUT_IDENTIFIER_PATTERN, $str, $matches);

		$value = '';
		$index += 1; // Increase to avoid selecting the full $str

		if (array_key_exists($index, $matches)) {
			$value = $matches[$index];
		}

		return $value;
	}

	/**
	 * Checks if the given string is a valid
	 * shortcut to be executed
	 *
	 * @param  string  $str
	 * @return boolean
	 */
	public static function isShortcut($str) {
		return preg_match(self::SHORTCUT_IDENTIFIER_PATTERN, $str);
	}

	/**
	 * Returns the base url
	 *
	 * @param  string $value
	 * @return string
	 */
	public static function execURL($value) {
		return UrlHelper::getBaseUrl($value);
	}

	/**
	 * Returns a translation string
	 *
	 * @param  string $value
	 * @return string
	 */
	public static function execLANG($value) {
		$translation =  Locale::get($value);
		return ! is_array($translation) ? $translation : '';
	}
}