<?php

namespace Fewlines\Helper\View;

use Fewlines\Http\Request as HttpRequest;
use Fewlines\Helper\UrlHelper;

class BaseUrl extends \Fewlines\Helper\AbstractViewHelper
{
	public function init()
	{
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
		return UrlHelper::getBaseUrl($parts);
	}
}