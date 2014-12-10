<?php
/**
 * fewlines CMS
 *
 * Description: Handles the config files
 *
 * @copyright Copyright (c) fewlines
 * @author Davide Perozzi
 */

namespace Fewlines\Application;

use Fewlines\Helper\DirHelper;
use Fewlines\Helper\PathHelper;

class Config
{
	public function __construct($configs)
	{
		$files = array();

		for($i = 0; $i < count($configs); $i++)
		{
			$dir = PathHelper::normalizePath($configs[$i]['dir']);
			$files[$dir] = DirHelper::getFilesByType($dir,
				$configs[$i]['type'], true);
		}

		echo "<pre>";
		print_r(DirHelper::flattenTree($files));
		echo "</pre>";
	}
}

?>