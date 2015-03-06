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
		$args = explode(",", preg_replace("/(.*)\((.*)\)/", "$2", $function));
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
		for($i = 0, $len = count($args[$i]); $i < $len; $i++)
		{
			$arg = trim($args[$i]);

			switch(true)
			{
				case preg_match(self::REGEX_STRING_MARKER, $arg):
					$args[$i] = preg_replace(self::REGEX_STRING_MARKER, "$1$2", $arg);

					pr($args[$i]);
				break;
			}
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