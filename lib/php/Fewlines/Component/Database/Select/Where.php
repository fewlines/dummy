<?php

namespace Fewlines\Component\Database\Select;

class Where
{
	/**
	 * The condition which is executed
	 *
	 * @var string
	 */
	private $condition;

	/**
	 * Operator between this and the next
	 * instance
	 *
	 * @var string
	 */
	private $operator;

	/**
	 * @param string $condition
	 * @param string $operator
	 */
	public function __construct($condition, $operator)
	{
		$this->condition = $condition;
		$this->operator  = $operator;
	}

	/**
	 * @return string
	 */
	public function getCondition()
	{
		return implode(" ", $this->condition);
	}

	/**
	 * @return string
	 */
	public function getOperator()
	{
		return $this->operator;
	}
}