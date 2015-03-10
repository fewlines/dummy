<?php

namespace Fewlines\Helper;

use Fewlines\Helper\ArrayHelper;
use Fewlines\Helper\UrlHelper;

class FunctionParseHelper
{
	/**
	 * @var string
	 */
	const DEFAULT_STRING_KEY = "defaultStr";

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
		$result    = self::executeStringFunctions($functions);

		return $result;
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
		$str = preg_replace(FNC_REGEX_PARSER, "%s", $str);

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
		$functions[self::DEFAULT_STRING_KEY] = $str;

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
		$args = preg_replace("/^(.*?)\((.*?)\)$/", "$2", $function);
		$args = self::castFunctionArguments(self::parseParanthesis($args));

		return array(
			"name" => $name,
			"args" => $args
		);
	}

	/**
	 * Transform the arguments of a function string 
	 * to a valid array
	 * 
	 * @param  string $str
	 * @return array
	 */
	private static function parseParanthesis($str)
	{
			$context 	  = array();
		    $contextStack = array(&$context);
		    $name 	      = '';
		    $strOpened    = false;

		    for($i = 0, $len = strlen($str); $i < $len; $i++) 
		    {
 				$name = trim($name);
		        
 				if($str[$i] == "'" || $str[$i] == '"')
 				{
 					if(true == $strOpened)
 					{
 						$strOpened = false;
 					}
 					else
 					{
 						$strOpened = true;
 					}
 				}

		        switch($str[$i]) 
		        {		        	
		            case ',':
		                if($name != '' && false == array_key_exists($name, $context))
		                {
		                   	$context[] = $name;
		                }

		                $name = '';
		            break;

		            case '(':
		            	if(true == $strOpened)
		            	{
		            		$name .= $str[$i];
		            	}
		            	else
		            	{
			                $context[$name] = array();
			                $contextStack[] = &$context;
			                $context 		= &$context[$name];
			               	
			               	$name = '';
		               	}
		            break;

		            case ')':
						if(true == $strOpened)
						{
							$name .= $str[$i];
						}
						else
						{
			                if($name != '' && false == array_key_exists($name, $context))
			                {
			                    $context[] = $name;
			                }
			                
			                array_pop($contextStack);
			                
			                if(count($contextStack) == 0)
			                {
			                	throw new Exception\FunctionParserUnmatchedParanthesisException(
			                		'Unmatched parenthesis: ' . $str
			                	);
			                }
			                
			                $context = &$contextStack[count($contextStack)-1];
			                $name 	 = '';
		                }
		            break;

		            default:
		                $name .= $str[$i];
		            break;
		        }
		    }
		    
		    if($name != '' && false == array_key_exists($name, $context))
		    {
		        $context[] = $name;
		    }
		    
		    if(count($contextStack) != 1) 
		    {
		    	throw new Exception\FunctionParserUnmatchedParanthesisException(
					'Unmatched parenthesis: ' . $str
				);
		    }
		   
		    return array_pop($contextStack);
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
		foreach($args as $key => $value)
		{
			if(true == is_array($value))
			{
				$args[$key] = self::castFunctionArguments($value);
			}
			else
			{
				switch(true)
				{
					/*case preg_match('/array/', $value):
						pr($value);
					break;*/

					case preg_match(self::REGEX_STRING_MARKER, $value):
						$args[$key] = preg_replace(self::REGEX_STRING_MARKER, "$1$2", $value);
					break;

					case preg_match('/false|true/', $value):
						$args[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
					break;

					case is_numeric($value):
						$args[$key] = (float) $value;
					break;

					default:
						throw new Exception\FunctionParserInvalidArgumentException('
							Invalid argument found: "' . $value . '"
						');
					break;
				}
			}
		}

		return $args;
	}

	/**
	 * @param  string $name
	 * @param  array  $params
	 * @return string
	 */
	private static function getFunctionString($name, $params)
	{
		$result = "";

		// Parse arguments
		foreach($params as $key => $value)
		{
			if(true == is_array($value))
			{
				$params[$key] = self::getFunctionString($key, $value);
			}
		}

		$params = ArrayHelper::clean($params);

		switch(strtolower($name))
		{
			case 'baseurl':
				$result .= (string) call_user_func_array('\Fewlines\Helper\UrlHelper::baseUrl', $params);
			break;

			default:
				$result .= (string) call_user_func_array($name, $params);
			break;	
		}

		return $result;
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
		$resultArray = array();
		$defaultStr  = "";

		// Get default string to buffer content
		if(array_key_exists(self::DEFAULT_STRING_KEY, $functions))
		{
			$defaultStr = $functions[self::DEFAULT_STRING_KEY];
			unset($functions[self::DEFAULT_STRING_KEY]);
		}

		$functions = ArrayHelper::flatten($functions);

		for($i = 0, $len = count($functions); $i < $len; $i++)
		{
			$details = self::getFunctionDetails($functions[$i]);
			$resultArray[] .= self::getFunctionString($details['name'], $details['args']);
		}

		$resultAll = $defaultStr;

		// Parse result with cached default content
		foreach($resultArray as $result)
		{
			$resultAll = preg_replace('/%s/', $result, $resultAll, 1);
		}

		return $resultAll;
	}
}