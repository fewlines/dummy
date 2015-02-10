<?php

namespace Fewlines\Helper\View;

class GetScript extends BaseUrl
{
	public function init()
	{
	}

	/**
	 * Gets a script with a relative path
	 *
	 * @param string $path
	 * @param string $jsPath
	 */
	public function getScript($path, $jsPath = 'lib/js/')
	{
		return '<script src="' . $this->baseUrl($jsPath . $path) . '"></script>';
	}
}