<?php
namespace Fewlines\Application;

use Fewlines\Http\Router;

class Environment
{
	/**
	 * @var string
	 */
	const TYPE_FLAG_SEPERTOR = ':';

	/**
	 * @var string
	 */
	const FLAG_FUNCTION_IDENTIFIER = 'is';

	/**
	 * @var \Fewlines\Http\Router
	 */
	private $router;

	/**
	 * @var array
	 */
	private $types = array();

	/**
	 * @var array
	 */
	private $urlPatterns = array();

	/**
	 * @var array
	 */
	private $hostnames = array();

	public function __construct() {
		$this->router = Router::getInstance();
	}

	/**
	 * @param string $type
	 * @param \Fewlines\Application\Environment
	 */
	public function addType($type) {
		$this->types[] = self::parseType($type);
		return $this;
	}

	/**
	 * @param array $types
	 */
	public function setTypes($types) {
		foreach ($types as $i => $type) {
			$types[$i] = self::parseType($type);
		}

		$this->types = $types;
	}

	/**
	 * @param  string $type
	 * @return \Fewlines\Application\Environment\EnvType
	 */
	public static function parseType($type) {
		if ( ! is_array($type)) {
			$type = explode(self::TYPE_FLAG_SEPERTOR, $type);
		}

		$name = array_shift($type);
		$flags = $type;

		return new Environment\EnvType($name, $flags);
	}

	/**
	 * @param string $name
	 * @return \Fewlines\Environment\EnvType
	 */
	private function getTypeByName($name) {
		foreach ($this->types as $type) {
			if ($type->getName() == $name) {
				return $type;
			}
		}

		throw new Environment\Exception\TypeNotFoundException(
			'Type "' . (string) $type . '" not found.'
		);
	}

	/**
	 * @param string $type
	 * @param string $name
	 * @return \Fewlines\Application\Environment
	 */
	public function addHostname($type, $name) {
		$this->hostnames[] = new Environment\Hostname($this->getTypeByName($type), $name);
		return $this;
	}

	/**
	 * @param string $type
	 * @param string $pattern
	 * @return \Fewlines\Application\Environment
	 */
	public function addUrlPattern($type, $pattern) {
		$this->urlPatterns[] = new Environment\UrlPattern($this->getTypeByName($type), $pattern);
		return $this;
	}

	/**
	 * @return \Fewlines\Application\Environment\EnvType|boolean
	 */
	private function checkUrlPatterns() {
		$this->checkTypes();

		$url = $this->router->getRequest()->getFullUrl();
		$type = false;

		for ($i = 0, $len = count($this->urlPatterns); $i < $len; $i++) {
			if (preg_match($this->urlPatterns[$i]->getPattern(), $url)) {
				$type = $this->urlPatterns[$i]->getType();
				break;
			}
		}

		return $type;
	}

	/**
	 * @return \Fewlines\Application\Environment\EnvType
	 */
	private function checkHostnames() {
		$this->checkTypes();

		$hostname = gethostname();
		$type = false;

		for ($i = 0, $len = count($this->hostnames); $i < $len; $i++) {
			if ($this->hostnames[$i]->getName() == $hostname) {
				$type = $this->hostnames[$i]->getType();
			}
		}

		return $type;
	}

	/**
	 * @throws Environment\Exception\NoTypesFoundException IF types empty
	 * @return boolean
	 */
	private function checkTypes() {
		if (empty($this->types)) {
			throw new Environment\Exception\NoTypesFoundException(
				'No types in the environment found. Please define
				at least 1.'
			);
		}

		return true;
	}

	/**
	 * @return \Fewlines\Application\Environment\EnvType
	 */
	private function getType() {
		$urlPatternsType = $this->checkUrlPatterns();
		$hostnamesType = $this->checkHostnames();

		if (false != $urlPatternsType && false != $hostnamesType) {
			if ($urlPatternsType->getName() == $hostnamesType->getName()) {
				return $urlPatternsType;
			}
		}
		else if (false != $hostnamesType) {
			return $hostnamesType;

		}
		else if (false != $urlPatternsType) {
			return $urlPatternsType;
		}

		$urlPatternsPrio = array_search($urlPatternsType, $this->types);
		$hostnamesPrio = array_search($hostnamesType, $this->types);

		if ($urlPatternsPrio && $hostnamesPrio) {
			$prio = min(array($urlPatternsPrio, $hostnamesPrio));
		}
		else if ($urlPatternsPrio && ! $hostnamesPrio) {
			$prio = $urlPatternsPrio;
		}
		else if ($hostnamesPrio && ! $urlPatternsPrio) {
			$prio = $hostnamesPrio;
		}

		return $this->types[$prio];
	}

	/**
	 * @return string
	 */
	public function get() {
		return $this->getType()->getName();
	}

	/**
	 * @param string $name
	 * @param array $args
	 */
	public function __call($name, $args) {
		if (preg_match_all('/' . self::FLAG_FUNCTION_IDENTIFIER . '/', $name)) {
			$flag = strtolower(end(explode(self::FLAG_FUNCTION_IDENTIFIER, $name)));
			return $this->getType()->hasFlag($flag);
		}
	}
}
