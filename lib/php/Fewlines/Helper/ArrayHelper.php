<?php
/**
 * fewlines CMS
 *
 * Description: Helper to handle some
 * array functions
 *
 * @copyright Copyright (c) fewlines
 * @author Davide Perozzi
 */

namespace Fewlines\Helper;

class ArrayHelper
{
	/**
	 * Flats a multidimensional array
	 * into one dimension
	 *
	 * @param  array $array
	 * @return array
	 */
	public static function flatten($array)
	{
		if(false == is_array($array))
		{
			return array($array);
		}

	    $result = array();

	    foreach($array as $value)
	    {
	        $result = array_merge($result, self::flatten($value));
	    }

	    return $result;
	}

	/**
	 * Cleans an array e.g. empty values will
	 * be removed
	 *
	 * @param  array $array
	 * @return array
	 */
	public static function clean($array)
	{
		// Remove empty elements
		$array = array_values(array_filter($array));

		return $array;
	}

	/**
	 * Checks if the array is an associative array or not
	 * 
	 * @param  array  $array
	 * @return boolean
	 */
	public static function isAssociative($array)
	{
		return array_keys($array) !== range(0, count($array) - 1);
	}
}

?>