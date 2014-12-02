<?php
/**
 * fewlines CMS
 *
 * Description: Converts given paths
 *
 * @copyright Copyright (c) fewlines
 * @author Davide Perozzi
 */

namespace Fewlines\Helper;

class PathHelper
{
	/**
	 * Returns a real path.
	 * For example if the leading slash is missin
	 * this function add's it at the and and
	 * returns the new path
	 *
	 * @param  string $path
	 * @return string
	 */
	public static function getRealPath($path)
	{
		return substr($path, -1) != '/' ? $path . '/' : $path;
	}

	/**
	 * Gets the real view no matter if there
	 * is a type give or not (if not it uses
	 * the default type defined)
	 *
	 * @param  string $view
	 * @return
	 */
	public static function getRealViewFile($view)
	{
		$type = defined('VIEW_FILETYPE') ? VIEW_FILETYPE : 'php';
		$file = $view;

		if(!preg_match('/\./', $file))
		{
			$file .= '.' . $type;
		}

		return $file;
	}

	/**
	 * Returns the defined view path
	 * as real path
	 *
	 * @param  string $view Optional view
	 * @return string
	 */
	public static function getRealViewPath($view = '')
	{
		$path = self::getRealPath(VIEW_PATH);

		if(!empty($view))
		{
			$path = $path . self::getRealViewFile($view);
		}

		return $path;
	}
}

?>