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
	 * Sets the 404 header
	 */
	public static function setHeader404() {
		header("HTTP/1.0 404 Not Found");
		header("Status: 404 Not Found");
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
