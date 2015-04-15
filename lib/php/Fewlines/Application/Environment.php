<?php
namespace Fewlines\Application;

use Fewlines\Http\Router;

class Environment
{
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

	/**
	 * @param string $type
	 */
	public function addType($type) {
		$this->types[] = $type;
	}

	/**
	 * @param array $types
	 */
	public function setTypes($types) {
		$this->types = $types;
	}

	/**
	 * @param string $type
	 * @param string $name
	 */
	public function addHostname($type, $name) {
		$this->hostnames[] = new Environment\Hostname($type, $name);
	}

	/**
	 * @param string $type
	 * @param string $pattern
	 */
	public function addUrlPattern($type, $pattern) {
		$this->urlPatterns[] = new Environment\UrlPattern($type, $pattern);
	}

	/**
	 * @return boolean
	 */
	private function checkUrlPatterns() {
		$this->checkTypes();

		$url = Router::getInstance()->getRequest()->getFullUrl();
		$type = $this->types[0];

		for ($i = 0, $len = count($this->urlPatterns); $i < $len; $i++) {
			if (preg_match($this->urlPatterns[$i]->getPattern(), $url)) {
				$type = $this->urlPatterns[$i]->getType();
				break;
			}
		}

		return $type;
	}

	/**
	 * @return array
	 */
	private function checkHostnames() {
		$this->checkTypes();

		$hostname = gethostname();
		$type = $this->types[0];

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
	 * @return boolean
	 */
	public function isLocal() {

		echo "pc: "; var_dump($this->checkHostnames()); echo "<br>";
		echo "url: "; var_dump($this->checkUrlPatterns());

		// return in_array($this->env, $this->locals);
		return true;
	}
}
