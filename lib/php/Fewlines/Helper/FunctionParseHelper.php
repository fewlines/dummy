<?php

namespace Fewlines\Helper;

use Fewlines\Helper\ArrayHelper;

class FunctionParseHelper
{
	/**
	 * @var string
	 */
	const REGEX_STRING_MARKER = "/\"(.*)\"|\'(.*)\'/";

	/**
	 * @var string
	 */
	const SEPERATOR = ";";

	/**
	 * Parse a line as a string to exetue and
	 * return the filtered (by the marker)
	 * functions
	 *
	 * @param  string $line
	 * @return string
	 */
	public static function parseLine($line)
	{
		$functions = self::getStringFunctions(trim($line));
		$strings   = self::executeStringFunctions($functions);
	}

	/**
	 * Returns the defined functions in a string
	 * as a string
	 *
	 * @param  string $str
	 * @return array
	 */
	public static function getStringFunctions($str)
	{
		preg_match_all(FNC_REGEX_PARSER, $str, $matches);

		$functions    = array();
		$resultsCount = count($matches[1]);

		if($resultsCount > 0)
		{
			for($i = 0, $len = $resultsCount; $i < $len; $i++)
			{
				$functions[] = explode(self::SEPERATOR, $matches[1][$i]);
			}
		}

		$functions = ArrayHelper::trimValues($functions, true);
		return $functions;
	}

	/**
	 * Gets the details of a funtion string
	 * including the name, and parameters
	 *
	 * @param  string $function
	 * @return array
	 */
	public static function getFunctionDetails($function)
	{
		$name = preg_replace("/\((.*)\)/", "", $function);
		
		function parse($s) 
		{
		    $context 	  = array();
		    $contextStack = array(&$context);
		    $name 	      = '';

		    for($i = 0, $len = strlen($s); $i < $len; $i++) 
		    {
 				$name = trim($name);
		        
		        switch($s[$i]) 
		        {
		            case ',':
		                if($name != '' && false == array_key_exists($name, $context))
		                {
		                   	$context[] = $name;
		                }

		                $name = '';
		            break;

		            case '(':
		                $context[$name] = array();
		                $contextStack[] = &$context;
		                $context 		= &$context[$name];
		               	
		               	$name = '';
		            break;

		            case ')':
		                if($name != '' && false == array_key_exists($name, $context))
		                {
		                    $context[] = $name;
		                }
		                
		                array_pop($contextStack);
		                
		                // if(count($contextStack) == 0) throw new \Exception('Unmatched parenthesis');
		                
		                $context = &$contextStack[count($contextStack)-1];
		                $name = '';
		            break;

		            default:
		                $name .= $s[$i];
		        }
		    }
		    
		    if($name != '' && false == array_key_exists($name, $context))
		    {
		        $context[$name] = array();
		    }
		    
		    // if(count($contextStack) != 1) throw new Exception('Unmatched parenthesis');
		   
		    return array_pop($contextStack);
		}

		$args = preg_replace("/^(.*?)\((.*?)\)$/", "$2", $function);
		//$args = preg_replace("/[,]/", "/R/", $args);
		
		pr(parse($args));
		exit;

		$args = explode("/R/", $args);
		$args = ArrayHelper::trimValues($args);
		$args = self::castFunctionArguments($args);

		return array(
			"name" => $name,
			"args" => $args
		);
	}

	/**
	 * Casts the parameters (strings) to the
	 * right value. E.g. "false" => false
	 *
	 * @param  array $args
	 * @return array
	 */
	public static function castFunctionArguments($args)
	{
		for($i = 0, $len = count($args); $i < $len; $i++)
		{
			$arg = trim($args[$i]);

			switch(true)
			{
				case preg_match('/^(.*)\((.*)\)$/', $arg):
					// Function
				break;

				case preg_match(self::REGEX_STRING_MARKER, $arg):
					$args[$i] = preg_replace(self::REGEX_STRING_MARKER, "$1$2", $arg);
				break;

				case preg_match('/false|true/', $arg):
					$args[$i] = filter_var($args[$i], FILTER_VALIDATE_BOOLEAN);
				break;

				case is_numeric($arg):
					$args[$i] = (float) $arg;
				break;	

				default:
					// Invalid value
				break;
			}

			pr($args[$i]);
		}

		return $args;
	}

	/**
	 * Execute functions which contains a string as
	 * a return value
	 *
	 * @param  array $functions
	 * @return string
	 */
	public static function executeStringFunctions($functions)
	{
		$result = "";

		for($i = 0, $len = count($functions); $i < $len; $i++)
		{
			$fnc = $functions[$i];

			if(true == is_array($fnc))
			{
				$result .= self::executeStringFunctions($fnc);
			}
			else
			{
				$details = self::getFunctionDetails($fnc);

			}
		}

		return $result;
	}
}