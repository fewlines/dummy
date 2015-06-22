<?php
namespace Fewlines\Core\Application;

use Fewlines\Core\Http\Router;

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
	 * @var \Fewlines\Core\Http\Router
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
	 * @param \Fewlines\Core\Application\Environment
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
	 * @return \Fewlines\Core\Application\Environment\EnvType
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
	 * @return \Fewlines\Core\Environment\EnvType
	 */
	private function getTypeByName($name) {
		foreach ($this->types as $type) {
			if ($type->getName() == $name) {
				return $type;
			}
		}

		throw new Environment\Exception\TypeNotFoundException(
			'Type "' . (string) $name . '" not found.'
		);
	}

	/**
	 * @param string $type
	 * @param string $name
	 * @return \Fewlines\Core\Application\Environment
	 */
	public function addHostname($type, $name) {
		$this->hostnames[] = new Environment\Hostname($this->getTypeByName($type), $name);
		return $this;
	}

	/**
	 * @param string $type
	 * @param string $pattern
	 * @return \Fewlines\Core\Application\Environment
	 */
	public function addUrlPattern($type, $pattern) {
		$this->urlPatterns[] = new Environment\UrlPattern($this->getTypeByName($type), $pattern);
		return $this;
	}

	/**
	 * @return \Fewlines\Core\Application\Environment\EnvType|boolean
	 */
	private function checkUrlPatterns() {
		$url = $this->router->getRequest()->getHost();
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
	 * @return EnvType
	 */
	private function checkHostnames() {
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
	 * @return \Fewlines\Core\Application\Environment\EnvType
	 */
	private function getType() {
		$urlPatternsType = $this->checkUrlPatterns();
		$hostnamesType = $this->checkHostnames();

		/**
		 * Only check for the types
		 * if they are not both empty
		 */

		if ($urlPatternsType || $hostnamesType) {
			/**
			 * Return only ONE of both types.
			 * At this point no comaprison
			 * is needed
			 */

			if ( ! $urlPatternsType) {
				return $hostnamesType;
			}

			if ( ! $hostnamesType) {
				return $urlPatternsType;
			}

			/**
			 * Gets the index of the given types
			 * to choose the type by a "priority"
			 */

			$urlPatternsIndex = array_search($urlPatternsType, $this->types);
			$hostnamesIndex = array_search($hostnamesType, $this->types);

			/**
			 * Determinate which index is the lowest.
			 * The type with the lowest index is automatically
			 * the one with the highest priority, because it has
			 * been inserted as the first type.
			 */

			if ($hostnamesIndex == $urlPatternsIndex) {
				return $hostnamesType;
			}

			if ($urlPatternsIndex < $hostnamesIndex) {
				return $urlPatternsType;
			}

			if ($hostnamesIndex < $urlPatternsIndex) {
				return $hostnamesType;
			}
		}

		/**
		 * Return the type with the highest
		 * priority, if none of the cases
		 * above matched
		 */

		if (count($this->types) > 0) {
			return $this->types[0];
		}

		return null;
	}

	/**
	 * @return string
	 */
	public function get() {
		$type = $this->getType();

		if ($type) {
			return $type->getName();
		}

		return "";
	}

	/**
	 * @param string $name
	 * @param array $args
	 */
	public function __call($name, $args) {
		if (preg_match_all('/' . self::FLAG_FUNCTION_IDENTIFIER . '/', $name)) {
			$flag = strtolower(end(explode(self::FLAG_FUNCTION_IDENTIFIER, $name)));
			$type = $this->getType();

			if(false != $type) {
				return $type->hasFlag($flag);
			}

			return false;
		}
	}
}
