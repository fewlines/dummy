<?php
/**
 * fewlines CMS
 *
 * Description: Catch all errors and throws
 * a ErrorException instead
 *
 * @copyright Copyright (c) fewlines
 * @author Davide Perozzi
 */

namespace Fewlines\Handler;

class Error
{
	/**
	 * Handles the error
	 *
	 * @param  int    $errno
	 * @param  string $errstr
	 * @param  string $errfile
	 * @param  int    $errline
	 * @throws ErrorException IF $exit == true
	 */
	public function handleError($errno, $errstr, $errfile, $errline)
	{
		$exit = false;
		$type = "";

		if(error_reporting() & $errno)
		{
			switch ($errno)
			{
				case E_USER_ERROR:
                	$type = 'Fatal Error';
                    $exit = true;
                break;
                case E_USER_WARNING:
                case E_WARNING:
                    $type = 'Warning';
                break;
                case E_USER_NOTICE:
                case E_NOTICE:
                case @E_STRICT:
                    $type = 'Notice';
                break;
                case @E_RECOVERABLE_ERROR:
                    $type = 'Catchable';
                break;
                default:
                    $type = 'Unknown Error';
                    $exit = true;
                break;
			}
		}

		if(true == $exit)
		{
			throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
		}
	}
}

?>