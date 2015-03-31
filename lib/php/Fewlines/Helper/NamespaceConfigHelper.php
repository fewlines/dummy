<?php
namespace Fewlines\Helper;

use \Fewlines\Application\Config;

class NamespaceConfigHelper
{
	/**
	 * Gets the namespaces registered in the
	 * fewlines config file Namespace.xml
	 * filtered by the libraray. E.g. php, js
	 *
	 * @param  string $lib
	 * @return array
	 */
	public static function getNamespaces($lib) {
		$namspaceElements = Config::getInstance()->getElementByPath('namespace');
		$namespaces = array();

		if($namspaceElements != false) {
			foreach($namspaceElements->getChildren() as $child) {
				$childLib = $child->getAttribute("lib");

				if($childLib == $lib) {
					$namespaces[$child->getName()] = $child->getContent();
				}
			}
		}

		return $namespaces;
	}
}