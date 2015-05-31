<?php
namespace Fewlines\Core\Application\Environment;

class UrlPattern
{
	/**
	 * @var \Fewlines\Core\Application\Environment\EnvType
	 */
	private $type;

	/**
	 * @var string
	 */
	private $pattern;

	/**
	 * @param string $type
	 * @param string $pattern
	 */
	public function __construct($type, $pattern) {
		$this->type = $type;
		$this->pattern = $pattern;
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
	public function getPattern() {
		return $this->pattern;
	}
}