<?php

namespace Fewlines\Xml;

use Fewlines\Helper\PathHelper;
use Fewlines\Helper\ArrayHelper;
use Fewlines\Xml\Tree;

class Xml
{
	/**
	 * Holds the plain xml element
	 *
	 * @var \Fewlines\Xml\Tree
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
		$this->tree = new Tree(new \SimpleXMLElement($file, 0, true));
	}

	/**
	 * Gets the tree instance
	 *
	 * @return array
	 */
	public function getTree()
	{
		return $this->tree;
	}

	/**
	 * Gets the tree Element
	 *
	 * @return \Fewlines\Xml\Tree\Element
	 */
	public function getTreeElement()
	{
		return $this->tree->getElement();
	}

	/**
	 * Gets all element by a
	 * path sequence. It creates
	 * the list only for the last path
	 * segment
	 *
	 * @param  string  $path
	 * @param  boolean $collect
	 * @return \Fewlines\Xml\Tree\Element|boolean|array
	 */
	public function getElementsByPath($path, $collect = true)
	{
		$parts       = explode("/", $path);
		$parts       = ArrayHelper::clean($parts);
		$rootName    = $parts[0];
		$treeElement = $this->getTreeElement();

		if($treeElement->getName() != $rootName)
		{
			return false;
		}

		$result     = $treeElement;
		$resultList = array();

		for($i = 1, $partsLen = count($parts); $i < $partsLen; $i++)
		{
			if(true == $collect && $i == $partsLen-1)
			{
				$resultList = $result->getChildrenByName($parts[$i]);
			}

			$result = $result->getChildByName($parts[$i]);
		}

		if(true == $collect)
		{
			if(false == empty($resultList))
			{
				return $resultList;
			}
			else
			{
				if(false == empty($result))
				{
					return array($result);
				}
				else
				{
					return array();
				}
			}
		}

		return $result;
	}

	/**
	 * Gets one element from
	 * the tree with a given path
	 * sequence
	 *
	 * @param  string $path
	 * @return \Fewlines\Xml\Tree\Element|boolean
	 */
	public function getElementByPath($path)
	{
		return $this->getElementsByPath($path, false);
	}
}