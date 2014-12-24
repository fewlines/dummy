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
				$src   = (array) $includes[$i]->attributes()['src'];
				$alias = (array) $includes[$i]->attributes()['alias'];

				if(true == is_null($src))
				{
					continue;
				}

				$path = PathHelper::getRelativePath($src[0], $this->file);

				if(true == is_null($alias) || true == empty($alias[0]))
				{
					$includePaths[] = $path;
				}
				else
				{
					$includePaths[$alias[0]] = $path;
				}
			}
		}
		else
		{
			$src   = (array) $includes->attributes()['src'];
			$alias = (array) $includes->attributes()['alias'];

			if(false == is_null($src))
			{
				$path = PathHelper::getRelativePath($src[0], $this->file);

				if(true == is_null($alias) || true == empty($alias[0]))
				{
					$includePaths[] = $path;
				}
				else
				{
					$includePaths[$alias[0]] = $path;
				}
			}
		}

		// Remove include flags from tree
		unset($this->tree['include']);

		foreach($includePaths as $alias => $path)
		{
			$path = PathHelper::addFilePrefix($path, "_");
			$xml  = new self($path);
			$tree = $xml->getTree();

			if(true == is_numeric($alias))
			{
				$elName = preg_replace("/\.xml|^_/", "", $xml->getBasename());
			}
			else
			{
				$elName = $alias;
			}

			$this->tree[$elName] = $tree;
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

	/**
	 * Gets the basename of the xml file
	 */
	public function getBaseName()
	{
		return pathinfo($this->file, PATHINFO_BASENAME);
	}
}

?>