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
use Fewlines\Helper\ArrayHelper;
use Fewlines\Xml\Xml;

class Config
{
	/**
	 * Holds the instance
	 *
	 * @var \Fewlines\Application\Config
	 */
	private static $instance;

	/**
	 * Holds the paths to the config files
	 *
	 * @var array
	 */
	private $configFiles = array();

	/**
	 * Holds the path to the config files
	 * which were loaded
	 *
	 * @var array
	 */
	private $loadedConfigFiles = array();

	/**
	 * The input of all files as an array
	 *
	 * @var array
	 */
	private $xmls  = array();

	/**
	 * Load config files
	 *
	 * @param array $configs
	 */
	public function __construct($configs)
	{
		/**
		 * @todo FIX the fetch of root elements
		 */

		if(false == is_null(self::$instance))
		{
			throw new Exception\ConfigJustInstantiatedException("
					The config object has already been instantiated.
					Use the static function \"getInstance\" instead.
				");
		}

		self::$instance = $this;
		$files          = array();

 		for($i = 0; $i < count($configs); $i++)
		{
			$dir = PathHelper::normalizePath($configs[$i]['dir']);
			$files[$dir] = DirHelper::getFilesByType($dir,
				$configs[$i]['type'], true);
		}

		$this->configFiles = DirHelper::flattenTree($files);

		// Create the config objects and push them
		$this->initConfigs();
	}

	/**
	 * Returns the instane created
	 *
	 * @return \Fewlines\Application\Config
	 */
	public static function getInstance()
	{
		return self::$instance;
	}

	/**
	 * Create config objects with xml
	 */
	private function initConfigs()
	{
		for($i = 0; $i < count($this->configFiles); $i++)
		{
			$filePath = $this->configFiles[$i];
			$filename = basename($filePath);
			$ignore   = preg_match("/^_(.*)$/", $filename);

			if(false == $ignore)
			{
				$this->xmls[] = new Xml($filePath);
				$this->loadedConfigFiles[] = $filePath;
			}
		}
	}

	/**
	 * Gets elements by path
	 * sequence (in all xml files)
	 *
	 * @param  string $path
	 * @return array
	 */
	public function getElementsByPath($path)
	{
		$elements = array();

		foreach($this->xmls as $xml)
		{
			$result = $xml->getElementsByPath($path);

			if(false != $result)
			{
				$elements[] = $result;
			}
		}

		return ArrayHelper::flatten($elements);
	}

	/**
	 * Gets a element by path
	 * sequence (searched in all xml files)
	 * Try to only use this if you know what
	 * you will get (e.g. a single element)
	 * Will Return the first result which is found.
	 *
	 * @param  string $path
	 * @return \Fewlines\Xml\Tree\Element|boolean
	 */
	public function getElementByPath($path)
	{
		foreach($this->xmls as $xml)
		{
			$result = $xml->getElementByPath($path);

			if(false != $result)
			{
				return $result;
			}
		}

		return false;
	}
}

?>