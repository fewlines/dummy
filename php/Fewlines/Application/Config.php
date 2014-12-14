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
use Fewlines\Xml\Xml;

class Config
{
	/**
	 * Holds the paths to the config files
	 *
	 * @var array
	 */
	private $configFiles = array();

	/**
	 * Load config files
	 *
	 * @param array $configs
	 */
	public function __construct($configs)
	{
		$files = array();

 		for($i = 0; $i < count($configs); $i++)
		{
			$dir = PathHelper::normalizePath($configs[$i]['dir']);
			$files[$dir] = DirHelper::getFilesByType($dir,
				$configs[$i]['type'], true);
		}

		$this->configFiles = DirHelper::flattenTree($files);
		$this->initConfigs();
	}

	/**
	 * Create config object per xml
	 */
	private function initConfigs()
	{
		$xml = new Xml($this->configFiles[0]);
		echo "<pre>";
		print_r($xml->getTree());
		echo "</pre>";
	}
}

?>