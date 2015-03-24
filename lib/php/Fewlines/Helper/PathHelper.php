<?php

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
	 * Creates a valid path from an array
	 *
	 * @param  array $parts
	 * @return string
	 */
	public static function createPath($parts)
	{
		return self::getRealPath(implode("/", $parts));
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
	 * @return string
	 */
	public static function getRealViewFile($view)
	{
		$type = defined('VIEW_FILETYPE') ? VIEW_FILETYPE : 'php';
		$file = $view;
		$info = pathinfo($file);

		if(false == array_key_exists('EXTENSION', $info))
		{
			$file .= '.' . $type;
		}

		return $file;
	}

	/**
	 * Returns the defined view path
	 * as real path
	 *
	 * @param  string $view
	 * @param  string $action
	 * @param  string $layout
	 * @return string
	 */
	public static function getRealViewPath($view = '', $action = '', $layout = '')
	{
		$path = self::getRealPath(VIEW_PATH);

		if(false == empty($layout))
		{
			$path .= self::getRealPath($layout);
		}

		if(false == empty($action) && false == empty($view))
		{
			$path .= $view . '/';
			$path .= self::getRealViewFile($action);
		}
		else if(false == empty($view))
		{
			$path .= self::getRealViewFile($view);
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

	/**
	 * Checks if the path is a absolute path
	 *
	 * @param  string  $path
	 * @return boolean
	 */
	public static function isAbsolute($path)
	{
		return substr($path, 0, 1) == '/';
	}
}