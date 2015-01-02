<?php
/**
 * fewlines CMS
 *
 * Description: Helper to handle urls
 *
 * @copyright Copyright (c) fewlines
 * @author Davide Perozzi
 */

namespace Fewlines\Helper;

use Fewlines\Http\Request as HttpRequest;

class UrlHelper extends \Fewlines\Helper\View\BaseUrl
{
	/**
	 * Returns the base url
	 *
	 * @param  string|array $parts
	 * @return string
	 */
	public static function getBaseUrl($parts = "")
	{
		if(is_array($parts))
		{
			$parts = implode("/", $parts);
		}

		return HttpRequest::getInstance()->getBaseUrl() . ltrim($parts, "/");
	}
}

?>