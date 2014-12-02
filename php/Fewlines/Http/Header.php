<?php
/**
 * fewlines CMS
 *
 * Description: HTTP header class
 *
 * @copyright Copyright (c) fewlines
 * @author Davide Perozzi
 */

namespace Fewlines\Http;

class Header
{
	/**
	 * Returns all current headers
	 *
	 * @return array
	 */
	public static function getHeaders()
	{
		return getallheaders();
	}

	/**
	 * Sets the 404 header
	 */
	public static function setHeader404()
	{
		header("HTTP/1.0 404 Not Found");
		header("Status: 404 Not Found"); // FastCGI
	}

	/**
	 * Returns the status code
	 *
	 * @return integer
	 */
	public static function getStatusCode()
	{
		return http_response_code();
	}
}

?>