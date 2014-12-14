<?php
/**
 * fewlines CMS
 *
 * Description: Handles the config files
 *
 * @copyright Copyright (c) fewlines
 * @author Davide Perozzi
 */

namespace Fewlines\Xml;

use Fewlines\Helper\PathHelper;

class Xml
{
	/**
	 * Holds the plain xml element
	 *
	 * @var \SimpleXMLElement
	 */
	private $tree;

	/**
	 * The config file loaded
	 *
	 * @var string
	 */
	private $file;

	/**
	 * Creates a new xml config object
	 *
	 * @param string $file
	 */
	public function __construct($file)
	{
		$this->file = $file;
		$this->tree = (array) new \SimpleXMLElement($file, 0, true);
		$this->includeXmls();
	}

	/**
	 * Include the xmls in the given xml
	 */
	private function includeXmls()
	{
		if(false == array_key_exists('include', $this->tree))
		{
			return;
		}

		$includes     = $this->tree['include'];
		$includePaths = array();

		if(is_array($includes))
		{
			for($i = 0; $i < count($includes); $i++)
			{
				$src = (array) $includes[$i]->attributes()['src'];

				if(true == is_null($src))
				{
					continue;
				}

				$includePaths[] = PathHelper::getRelativePath($src[0], $this->file);
			}
		}
		else
		{
			$src = (array) $includes->attributes()['src'];

			if(false == is_null($src))
			{
				$includePaths[] = PathHelper::getRelativePath($src[0], $this->file);
			}
		}

		unset($this->tree['include']);

		for($i = 0; $i < count($includePaths); $i++)
		{
			$xml = new self($includePaths[$i]);
			$tree = $xml->getTree();
			$this->tree[] = $tree;
		}
	}

	/**
	 * Gets the tree
	 *
	 * @return array
	 */
	public function getTree()
	{
		return $this->tree;
	}
}

?>