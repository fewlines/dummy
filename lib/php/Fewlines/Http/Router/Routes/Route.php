<?php
namespace Fewlines\Http\Router\Routes;

class Route
{
	/**
	 * @var string
	 */
	const TO_SEPERATOR = '@';

	/**
	 * @var string
	 */
	private $type;

	/**
	 * @var string
	 */
	private $from;

	/**
	 * @var string
	 */
	private $to;

	/**
	 * @param string $type
	 * @param string $from
	 * @param string $to
	 */
	public function __construct($type, $from, $to) {
		$this->type = $type;
		$this->from = $from;
		$this->to = $to;
	}

	/**
	 * @param string $to
	 */
	public function setType($type) {
		$this->type = $type;
	}

	/**
	 * @param string $form
	 */
	public function setFrom($from) {
		$this->from = $from;
	}

	/**
	 * @param string $to
	 */
	public function setTo($to) {
		$this->to = $to;
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
	public function getFrom() {
		return $this->from;
	}

	/**
	 * @return string
	 */
	public function getToClass() {
		$parts = explode(self::TO_SEPERATOR, $this->to);
		return $parts[0];
	}

	/**
	 * @return string
	 */
	public function getToMethod() {
		$parts = explode(self::TO_SEPERATOR, $this->to);
		return $parts[1];
	}
}
