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

class Config
{
	public function __construct($configs)
	{
		for($i = 0; $i < count($configs); $i++)
		{
			$files = DirHelper::getFilesByType(
				$configs[$i]['dir'], $configs[$i]['type']);
		}
	}
}

?>