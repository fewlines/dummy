<?php

namespace Fewlines\Core\Xml;

use Fewlines\Core\Xml\Tree\Element;

class Tree
{
	/**
	 * Array of all elements
	 * (recursive)
	 *
	 * @var \Fewlines\Core\Xml\Tree\Element
	 */
	private $tree;

	/**
	 * Init the tree and create elements
	 *
	 * @param \SimpleXmlElement $root
	 */
	public function __construct(\SimpleXmlElement $root)
	{
		// "Transform" the node to a element
		$this->tree = new Element($root->getName(),
			(array) $root->attributes(), trim((string) $root));

		// Add all child elements
		foreach($root as $node)
		{
			$this->addChild($node, $this->tree);
		}
	}

	/**
	 * Add the node to a position under the tree
	 *
	 * @param \SimpleXmlElement $node
	 * @param \Fewlines\Core\Xml\Tree\Element $parent
	 */
	public function addChild(\SimpleXmlElement $node, $parent)
	{
		if(false == $node instanceof \SimpleXmlElement)
		{
			return;
		}

		$name       = $node->getName();
		$attributes = (array) $node->attributes();
		$content    = trim((string) $node);
		$element    = new Element($name, $attributes, $content);

		$parent->addChild($element);

		// Add child elements recursive
		if($node->count() > 0)
		{
			foreach($node as $childNode)
			{
				$this->addChild($childNode, $element);
			}
		}
	}

	/**
	 * Returns the element of the tree
	 *
	 * @return \Fewlines\Core\Xml\Tree\Element
	 */
	public function getElement()
	{
		return $this->tree;
	}
}