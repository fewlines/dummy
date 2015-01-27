<?php 

namespace Fewlines\Csv;

class Csv
{
	/**
	 * Caches csv file to prevent reloading
	 * 
	 * @var array
	 */
	private static $cache = array();

	/**
	 * Create a new csv instance to operate with
	 * 
	 * @param string $file 
	 */
	public function __construct($file)
	{
		self::getFile($file);
	}

	/**
	 * Gets the content of a csv file 
	 * and saves it as array
	 * 
	 * @param  string $file 
	 * @return array
	 */
	public static function getFile($file)
	{
		$data = array();

		if(array_key_exists($file, self::$cache))
		{
			// Get data from cache
			$data = self::$cache[$file];
		}
		else
		{
			// Get new file
			if(($handle = fopen($file, "r")) !== false)
			{
				while(($row = fgetcsv($handle)) !== false)
				{
					$data[] = $row;
				}

				fclose($handle);
			}

			// Cache data
			self::$cache[$file] = $data;
		}

		return $data;
	}

	/**
	 * Get a value from a 2 column grid
	 *
	 * @param  string $file
	 * @param  string $key
	 * @return string|boolean
	 */
	public static function getValue($file, $key = "")
	{
		$data = self::transform(self::getFile($file), 2);

		if(array_key_exists($key, $data))
		{
			return $data[$key];
		}

		return false;
	}

	/**
	 * Transforms a grid to defined columns 
	 * e.g. locales
	 * 
	 * @param  array   $data
	 * @param  integer $columns 
	 * @return array
	 */
	private static function transform($data, $columns)
	{
		$transformedData = array();

		foreach($data as $row)
		{
			switch($columns)
			{
				// 2 columns
				case 2:
					if(count($row) == $columns)
					{
						$transformedData[$row[0]] = $row[1];
					}
				break;
			}
		}

		return $transformedData;
	}
}