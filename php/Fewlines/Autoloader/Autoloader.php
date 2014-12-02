<?php
/**
 * fewlines CMS
 *
 * Description: Loads a file using the use
 * keyword
 *
 * @copyright Copyright (c) fewlines
 * @author Davide Perozzi
 */

namespace Fewlines\Autoloader;

class Autoloader
{
	/**
	 * Loads a class with the given path
	 *
	 * @param  string $path
	 * @return boolean
	 */
	static public function loadClass($path)
	{
		$file = str_replace('\\', '/', $path) . '.php';

		if(file_exists(FEWLINES_PHP . '/' . $file))
		{
			require_once($file);

			if(class_exists(basename($path)))
			{
				return true;
			}
		}

		return false;
	}
}

?>