<?php
/**
 * fewlines CMS
 *
 * Description: Url helper for views
 *
 * @copyright Copyright (c) fewlines
 * @author Davide Perozzi
 */

namespace Fewlines\Helper\View;

use Fewlines\Http\Request as HttpRequest;

class BaseUrl extends \Fewlines\Helper\ViewHelper
{
	/**
	 * Holds the current http request
	 *
	 * @var \Fewlines\Http\Request
	 */
	private $httpRequest;

	public function init()
	{
		$this->httpRequest = HttpRequest::getInstance();
	}

	/**
	 * Returns the baseurl with the optional
	 * part, which will be appended
	 *
	 * @param  string|array $parts
	 * @return string
	 */
	public function baseUrl($parts = "")
	{
		if(is_array($parts))
		{
			$parts = implode("/", $parts);
		}

		return $this->httpRequest->getBaseUrl() . ltrim($parts, "/");
	}
}

?>