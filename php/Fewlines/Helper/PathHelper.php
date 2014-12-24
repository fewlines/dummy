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
	 * For example if the leading slash is missing
	 * this function add's it at the and and
	 * returns the new path
	 *
	 * @param  string $path
	 * @return string
	 */
	public static function getRealPath($path)
	{
		$path = self::normalizePath($path);
		return substr($path, -1) != '/' ? $path . '/' : $path;
	}

	/**
	 * Gets the relative path of 2 given
	 * parts
	 *
	 * @param  string $relPath
	 * @param  string $from
	 * @return string
	 */
	public function getRelativePath($relPath, $from)
	{
		$isFile = !is_dir($from);
		$fromDir = $isFile ? pathinfo($from, PATHINFO_DIRNAME) : $from;
		$realPath = rtrim($fromDir, "/") . "/";

		preg_match_all("/\.\.\//", $relPath, $back);
		$back = $back[0];

		for($i = 0; $i < count($back); $i++)
		{
			$realPath .= "../";
		}

		$realPath = self::normalizePath(realpath($realPath));

		if(false == is_dir($relPath))
		{
			$realPath = rtrim($realPath, "/") . "/" . pathinfo($relPath, PATHINFO_BASENAME);
		}

		return $realPath;
	}

	/**
	 * Normalizes path, so all paths
	 * will be the same after using this
	 * function
	 *
	 * @param  string $path
	 * @return string
	 */
	public static function normalizePath($path)
	{
		$path = preg_replace('/\\\/', '/', $path);
		return $path;
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

	/**
	 * Adds a prefix to a file name
	 *
	 * @param string  $path
	 * @param string  $prefix
	 * @param boolean $prepend
	 */
	public static function addFilePrefix($path, $prefix, $prepend = true)
	{
		$filename   = pathinfo($path, PATHINFO_FILENAME);
		$extension  = '.' . pathinfo($path, PATHINFO_EXTENSION);
		$prefixPath = pathinfo($path, PATHINFO_DIRNAME) . '/';

		if(false == $prepend)
		{
			$prefixPath .= $filename . $prefix . $extension;
		}
		else
		{
			$prefixPath .= $prefix . $filename . $extension;
		}

		return $prefixPath;
	}
}

?>