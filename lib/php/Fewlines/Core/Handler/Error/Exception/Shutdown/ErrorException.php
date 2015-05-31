<?php

namespace Fewlines\Core\Handler\Error\Exception\Shutdown;

class ErrorException extends \ErrorException
{
	/**
	 * Message of the ShutdownException
	 *
	 * @var string
	 */
	public $message = "";

	/**
	 * Error file
	 *
	 * @var string
	 */
	public $file = "";

	/**
	 * Line of the error
	 *
	 * @var integer
	 */
	public $line = 0;

	/**
	 * Set the message
	 *
	 * @param string $message
	 */
	public function setMessage($message)
	{
		$this->message = $message;
	}

	/**
	 * Set the file path
	 *
	 * @param string $file
	 */
	public function setFile($file)
	{
		$this->file = $file;
	}

	/**
	 * Set line of the error
	 *
	 * @param string $line
	 */
	public function setLine($line)
	{
		$this->line = $line;
	}
}