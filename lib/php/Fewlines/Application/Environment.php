<?php
namespace Fewlines\Application;

class Environment
{
	/**
	 * @var string
	 */
	private $env;

	/**
	 * @var array
	 */
	private $locals = array(
			'development', 'testing', 'test', 'local'
		);

	/**
	 * Returns the current
	 * environment
	 *
	 * @return string
	 */
	public function get() {
		return $this->env;
	}

	/**
	 * @param string $env
	 */
	public function set($env) {
		$this->env = $env;
	}

	/**
	 * @param string $name
	 */
	public function addLocal($name) {
		$this->locals[] = $name;
	}

	/**
	 * @return boolean
	 */
	public function isLocal() {
		return in_array($this->env, $this->locals);
	}
}