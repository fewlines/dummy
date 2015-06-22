<?php
namespace Fewlines\Core\Http;

use Fewlines\Core\Template\Template;
use Fewlines\Core\Application\Buffer;

class Header
{
	use Messages;

	/**
	 * @var integer
	 */
	const DEFAULT_ERROR_CODE = 500;

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
	 * Returns the active status code
	 *
	 * @return integer
	 */
	public static function getStatusCode() {
		return http_response_code();
	}

	/**
	 * Returns the status message of the current
	 * status code
	 *
	 * @param  boolean $real if set the default message wil be returned
	 * @return string
	 */
	public static function getStatusMessage($real = false) {
		$code = self::getStatusCode();
		$message = '';

		if (array_key_exists($code, self::$messages)) {
			if (true == $real) {
				if ( ! empty(self::$messages[$code]['status'])) {
					$message = self::$messages[$code]['status'];
				}
			}
			else {
				if (empty(self::$messages[$code]['message'])) {
					$message = self::$messages[$code]['status'];
				}
				else {
					$message = self::$messages[$code]['message'];
				}
			}
		}

		return $message;
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
			$code = self::DEFAULT_ERROR_CODE;
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

			/**
			 * Clear previous outputs
			 * completely
			 */

			Buffer::clear(true);

			/**
			 * Render new template
			 * with the given view path
			 * and layout
			 */

			Template::getInstance()
				->setLayout(self::$codeViews[$code]['layout'])
				->setView(self::$codeViews[$code]['path'])
				->renderAll();

			// Abort to prevent further actions
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
	 * @param number  $code
	 * @param string  $path
	 * @param boolean $condition
	 */
	public static function setCodeView($code, $path, $condition = true, $layout = '') {
		if (true == $condition) {
			self::$codeViews[$code] = array(
				'path'   => $path,
				'layout' => empty($layout) ? DEFAULT_LAYOUT : $layout
			);
		}
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
