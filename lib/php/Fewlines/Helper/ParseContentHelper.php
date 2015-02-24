<?php 

namespace Fewlines\Helper;

use Fewlines\Helper\ArrayHelper;

class ParseContentHelper
{
	public static function parseLine($line)
	{
		if(true == preg_match('/\{\{.*\}\}/', $line))
		{
			$line = preg_replace('/\{\{|\}\}/', '', $line);	

			// Get function tiles
			$fncName    = preg_replace('/\((.*)\)/', '', $line);
			$parameters = explode(",", preg_replace('/(.*)\(|\)/', '', $line));
			$parameters = ArrayHelper::trimValues($parameters);

			pr($parameters);
		}

		return $line;
	}
}