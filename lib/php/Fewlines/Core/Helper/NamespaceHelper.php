<?php
namespace Fewlines\Core\Helper;

use Fewlines\Core\Application\Config;

class NamespaceHelper
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
		$namespaceElements = Config::getInstance()->getElementsByPath('namespace');
		$namespaces = array();

		if($namespaceElements) {
			for ($i = 0, $len = count($namespaceElements); $i < $len; $i++) {
				foreach ($namespaceElements[$i]->getChildren() as $child) {
					$childLib = $child->getAttribute("lib");

					if($childLib == $lib) {
						$namespaces[$child->getName()] = $child->getContent();
					}
				}
			}
		}

		return $namespaces;
	}


	/**
	 * Gets a namespace path from the
	 * config by the name of element
	 * in the config file
	 *
	 * @param  string $name
	 * @return string
	 */
	public static function getNamespace($name, $lib) {
		foreach (self::getNamespaces($lib) as $nsName => $path) {
			if ($nsName == $name) {
				return $path;
			}
		}

		return "";
	}
}