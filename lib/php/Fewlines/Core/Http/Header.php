<?php
namespace Fewlines\Core\Http;

use Fewlines\Core\Template\Template;
use Fewlines\Core\Application\Buffer;

class Header
{
	use Messages;

	/**
	 * @var array
	 */
	private static $codeViews = array();

	/**
	 * Returns all current headers
	 *
	 * @return array
	 */
	public static function getHeaders() {
		return getallheaders();
	}

	/**
	 * Sets the http status of the current
	 * request/response
	 */
	private static function setStatus($str) {
		header('HTTP/1.0 ' . $str);
		header('Status: ' . $str);
	}

	/**
	 * Sets the defined code
	 *
	 * @param number $code
	 */
	public static function set($code, $throw = true) {
		// Check if status message is given
		if ( ! array_key_exists($code, self::$messages)) {
			$code  = 500;
		}

		// Build message
		$message = self::$messages[$code]['status'];

		if ( ! empty(self::$messages[$code]['message'])) {
			$message = self::$messages[$code]['message'];
		}

		// Set status to the header
		self::setStatus(self::$messages[$code]['status']);

		// Check if a view was set
		if (array_key_exists($code, self::$codeViews)) {
			$throw = false;

			\Fewlines\Core\Application\Buffer::clear(true);
			Template::getInstance()->setLayout('default')->setView(self::$codeViews[$code])->renderAll();

			exit;
		}
		else {
			if(true == $throw) {
				throw new Header\Exception\HttpException($message);
			}
		}
	}

	/**
	 * Sets the url of a code so it will be rendered
	 * instead of the exception
	 *
	 * @param number $code
	 * @param string $path
	 */
	public static function setCodeView($code, $path) {
		self::$codeViews[$code] = $path;
	}

	/**
	 * Redirects the user
	 *
	 * @param  string $location
	 */
	public static function redirect($location) {
		Buffer::clear(true);

		header("Location: " . $location);
		exit;
	}
}
