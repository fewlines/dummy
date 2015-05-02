<?php 

namespace Fewlines\Application\Environment;

class EnvType 
{
	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var array
	 */
	private $flags = array();

	/**
	 * @param string $name
	 * @param array $flags
	 */
	public function __construct($name, $flags) {
		$this->name = $name;
		$this->flags = $flags;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;				
	}

	/**
	 * @return string
	 */
	public function getFlags() {
		return $this->flags;
	}

	/**
	 * @param  string  $flag
	 * @return boolean      
	 */
	public function hasFlag($flag) {
		return in_array($flag, $this->flags);
	}
}