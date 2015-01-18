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
}

?>