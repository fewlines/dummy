<?php
namespace Fewlines\Http;

class Header
{
	/**
	 * Returns all current headers
	 *
	 * @return array
	 */
	public static function getHeaders() {
		return getallheaders();
	}

	/**
	 * Sets the defined code
	 *
	 * @param number $code
	 */
	public static function set($code) {
		switch ($code) {
			case 404:
				header('HTTP/1.0 404 Not Found');
				header('Status: 404 Not Found');

				throw new Header\Exception\HttpNotFoundException(
					'The page couldn\'t be found'
				);

				break;
		}
	}

	/**
	 * Redirects the user
	 *
	 * @param  string $location
	 */
	public static function redirect($location) {
		header("Location: " . $location);
		exit;
	}
}
