<?php
/**
 * fewlines CMS
 *
 * Description: A interface for the native
 * php sessions and cookies
 *
 * @copyright Copyright (c) fewlines
 * @author Davide Perozzi
 */

namespace Fewlines\Session;

class Session
{
	/**
	 * Namepsace for cookie
	 *
	 * @var string
	 */
	const COOKIE = 'cookie';

	/**
	 * Namepsace for native session
	 *
	 * @var string
	 */
	const SESSION = 'session';

	/**
	 * Tells if the native session
	 * was started
	 *
	 * @var boolean
	 */
	private static $isStarted = false;

	/**
	 * Tells if the session is a cookie
	 * or a native session
	 *
	 * @var string
	 */
	private $type;

	/**
	 * Holds all created sessions
	 *
	 * @var array
	 */
	private static $sessions;

	/**
	 * Creates a new session
	 *
	 * @param string $name
	 * @param *	     $content
	 * @param int 	 $lifetime (minutes)
	 */
	public function __construct($name, $content, $lifetime = 0)
	{
		$this->type = self::COOKIE;

		if($lifetime == 0)
		{
			$this->type = self::SESSION;
		}

		// Create a session by type
		switch($this->type)
		{
			case self::COOKIE:
				$this->createCookie($name, $content, $lifetime);
			break;

			case self::SESSION:
				$this->createSession($name, $content);
			break;
		}
	}

	/**
	 * Starts the session after a wipeout
	 */
	public static function startSession()
	{
		ob_clean();
		session_start();

		self::$isStarted = true;
	}

	public static function isStarted()
	{
		return self::$isStarted;
	}

	/**
	 * Creates a cookie
	 *
	 * @param string $name
	 * @param *      $content
	 * @param int    $lifetime
	 */
	private function createCookie($name, $content, $lifetime)
	{

	}

	/**
	 * Creates a session
	 *
	 * @param  string $name
	 * @param  *      $content
	 */
	private function createSession($name, $content)
	{
		if(false == self::$isStarted)
		{
			throw new Exception\SessionNotStartedException(
				"Please start the session first (before any output)"
			);
		}
	}

	/**
	 * Tells it the session is a cookie
	 *
	 * @return boolean
	 */
	public function isCookie()
	{
		return $this->type == self::COOKIE;
	}

	/**
	 * Tells it the session is a native
	 * session
	 *
	 * @return boolean
	 */
	public function isSession()
	{
		return $this->type == self::SESSION;
	}
}

?>