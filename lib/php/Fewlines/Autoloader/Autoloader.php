<?php

namespace Fewlines\Autoloader;

class Autoloader
{
	/**
	 * Simply loads a class with
	 * the given path (trimmed to the basics)
	 *
	 * @param  string $path
	 * @return boolean
	 */
	static public function loadClass($path)
	{
		$file = str_replace('\\', '/', $path) . '.php';

		if(file_exists(FEWLINES_PHP . '/' . $file))
		{
			require_once $file;

			if(class_exists(basename($path)))
			{
				return true;
			}
		}

		return false;
	}
}