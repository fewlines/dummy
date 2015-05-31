<?php
namespace Fewlines\Core\Http;

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
	public static function set($code, $throw = true) {
		switch ($code) {
			case 404:
				header('HTTP/1.0 404 Not Found');
				header('Status: 404 Not Found');

				if(true == $throw) {
					throw new Header\Exception\HttpNotFoundException(
						'The page couldn\'t be found'
					);
				}

				break;

			default:
			case 500:
				header('HTTP/1.1 500 Internal Server Error');
				header('Status: 500 Internal Server Error');

				if(true == $throw) {
					throw new Header\Exception\InternalServerErrorException(
						'Something went wrong'
					);
				}

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
