<?php
/**
 * fewlines CMS
 *
 * Description: Helper to scan dirs
 *
 * @copyright Copyright (c) fewlines
 * @author Davide Perozzi
 */

namespace Fewlines\Helper;

class DirHelper
{
	/**
	 * Get files in the given dir
	 * with the existing file extension
	 *
	 * @param  string  $filetype
	 * @param  string  $dir
	 * @param  boolean $recursive
	 * @return array
	 */
	public static function getFilesByType($dir, $filetype, $recursive = false)
	{
		$elements = self::scanDir($dir, $recursive);

		for($i = 0; $i < count($elements); $i++)
		{
			echo "<pre>";
			print_r($elements);
			echo "</pre>";
		}
	}

	/**
	 * Scans a dir and return the content
	 * in it
	 *
	 * @param  string  $dir
	 * @param  boolean $recursive
	 * @return array
	 */
	public static function scanDir($dir, $recursive = false)
	{
		$tree = array();

		foreach(scandir($dir) as $result)
		{
			if($result == '.' || $result == '..')
			{
				continue;
			}

			$resultPath = PathHelper::getRealPath($dir) . $result;

			if(true == $recursive && is_dir($resultPath))
			{
				$tree[] = array(
					'type' => 'dir',
					'name' => $result,
					'path' => $resultPath,
					'content' => self::scanDir($resultPath)
				);

				continue;
			}

			$tree[] = array(
				'type' => is_dir($resultPath) ? 'dir' : 'file',
				'name' => $result,
				'path' => $resultPath
			);
		}

		return $tree;
	}
}

?>