<?php
namespace Fewlines\Core\Application\Environment;

class Hostname
{
	/**
	 * @var \Fewlines\Core\Application\Environment\EnvType
	 */
	private $type;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @param string $type
	 * @param string $name
	 */
	public function __construct($type, $name) {
		$this->type = $type;
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
}