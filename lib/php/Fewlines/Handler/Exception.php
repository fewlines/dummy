<?php

namespace Fewlines\Handler;

class Exception
{

	/**
	 * @var string
	 */
	const LINE_SEPERATOR = ':';

	/**
	 * Parse exception details
	 *
	 * @param \Exception $err
	 */
	public function __construct(\Exception $err)
	{
		pr($err->getFile() . ':' . $err->getLine());
		pr($err->getTrace());
	}
}