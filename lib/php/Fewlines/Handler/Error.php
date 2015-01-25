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

use Fewlines\Application\Application;

class Error
{
	/**
	 * The message which appears if the application shuts down
	 *
	 * @var string
	 */
	const SHUTDOWN_ERROR_MESSAGE = "<b>Shutdown</b> by an error";

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
                	$type = 'FatalError';
                    $exit = true;
                break;
                case E_USER_WARNING:
                case E_WARNING:
                    $type = 'Warning';

                    // Output warning
  					echo "<b>Warning: </b>" . $errstr;
                break;
                case E_USER_NOTICE:
                case E_NOTICE:
                case @E_STRICT:
                    $type = 'Notice';
                    $exit = true;
                break;
                case @E_RECOVERABLE_ERROR:
                    $type = 'Catchable';
                break;
                default:
                    $type = 'UnknownError';
                    $exit = true;
                break;
			}
		}

		if(true == $exit)
		{
			$className = "\Fewlines\Handler\Error\Exception\\" . $type . "Exception";
			throw new $className($errstr, 0, $errno, $errfile, $errline);
		}
	}

	public function handleShutdown()
	{
		$isError = false;
		$type    = "UnknownError";

	    if($error = error_get_last())
	    {
		    switch($error['type'])
		    {
		        case E_ERROR:
		        	$type = "FatalError";
		        case E_CORE_ERROR:
		        	$type = "CoreError";
		        case E_COMPILE_ERROR:
		        	$type = "CompileError";
		        case E_USER_ERROR:
		        	$type = "UserError";
		        case E_PARSE:
		        	$type = "ParseError";
		            $isError = true;
		        break;
		    }

		    if(true == $isError)
		    {
				// Create new exception
				$className = "\Fewlines\Handler\Error\Exception\Shutdown\\" . $type . "Exception";
		    	$exception = new $className;	

		    	if(false == is_null($exception))
		    	{
		    		$message = (true == array_key_exists('message', $error)) ? $error['message'] : $type;
			    	$exception->setMessage(self::SHUTDOWN_ERROR_MESSAGE . ": " . $message);

			    	// Define position
			    	if(array_key_exists('line', $error) && 
			    		array_key_exists('file', $error))
			    	{
			    		$exception->setFile($error['file']);
			    		$exception->setLine($error['line']);
			    	}	

			    	// Render new application after wipeout
				    Application::renderShutdownError($exception);
			    }
			}
		}
	}
}

?>