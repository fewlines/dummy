<?php
/**
 * fewlines CMS
 *
 * Description: A "interface" for the native
 * php sessions and cookies
 *
 * @copyright Copyright (c) fewlines
 * @author Davide Perozzi
 */

namespace Fewlines\Crypt;

class Crypt
{
	/**
	 * A random key to optimize
	 * the encryption of strings
	 *
	 * @var string
	 */
	const KEY = '§!(=$&/)%ERWZQT';

	/**
	 * Hashes a string
	 *
	 * @param  string $str
	 * @return string
	 */
	public function hash($str)
	{
		$chars = str_split($str);
		$hashmap = array();
		$securityKey = defined(SECURITY_KEY)
						? SECURITY_KEY
						: self::KEY;

		for($i = 0; $i < count($chars); $i++)
		{
			$hashmap[] = md5(sha1($chars[$i] . $i*42 . md5($securityKey)));
		}

		$charSet = ceil($hashmap[0] / count($hashmap));
		$convHashmap = array();

		for($i = 0; $i < count($hashmap); $i++)
		{
			$convHashmap[] = substr($hashmap[$i], 0, $charSet);
		}

		$hashString = md5(sha1(implode($securityKey, $convHashmap)));

		return $hashString;
	}

	/**
	 * Encrypts the given string
	 *
	 * @param  string $str
	 * @return string
	 */
	public function encrypt($str)
	{
		/**
		 * @todo Write enryption algorithm
		 */

		return $str;
	}

	/**
	 * Decrypts the given string
	 *
	 * @param  string $str
	 * @return string
	 */
	public function decrypt($str)
	{
		/**
		 * @todo Write decyption algorithm
		 */

		return $str;
	}
}

?>