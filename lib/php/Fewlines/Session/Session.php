<?php

namespace Fewlines\Session;

use Fewlines\Session\Cookie\Cookie as NativeCookie;
use Fewlines\Session\Cookie\Session as NativeSession;
use Fewlines\Http\Request as HttpRequest;
use Fewlines\Session\Result;

class Session
{
	/**
	 * Prefix for each session
	 *
	 * @var string
	 */
	const PREFIX = 'fl_';

	/**
	 * Key for cookie
	 *
	 * @var string
	 */
	const COOKIE = 'cookie';

	/**
	 * Key for native session
	 *
	 * @var string
	 */
	const SESSION = 'session';

	/**
	 * Holds the namespace for the cookie
	 *
	 * @var string
	 */
	const COOKIE_NAMESPACE = 'Fewlines\Session\Cookie\Cookie';

	/**
	 * Holds the namespace for the session
	 *
	 * @var string
	 */
	const SESSION_NAMESPACE = 'Fewlines\Session\Cookie\Session';

	/**
	 * Tells if the native session
	 * was started
	 *
	 * @var boolean
	 */
	private static $isStarted = false;

	/**
	 * Holds all created sessions
	 *
	 * @var array
	 */
	private static $sessions = array();

	/**
	 * Creates a new session
	 *
	 * @param string  $name
	 * @param *	      $content
	 * @param int 	  $lifetime (minutes)
	 * @param boolean $encrypt
	 * @param string  $path
	 */
	public function __construct($name, $content, $lifetime = 0, $encrypt = false, $path = '')
	{
		$cookiePath = HttpRequest::getInstance()->getBaseUrl();

		if($path != '')
		{
			$cookiePath .= $path;
		}

		$this->type = $lifetime == 0 ? self::SESSION : self::COOKIE;

		// Create a session by type
		switch($this->type)
		{
			case self::COOKIE:
				$this->createCookie($name, $content, (time() + $lifetime * 60), $encrypt, $cookiePath);
			break;

			case self::SESSION:
				$this->createSession($name, $content);
			break;
		}
	}

	/**
	 * Maps the cookie with the suffix and
	 * filters the cookies from the project
	 *
	 * @return
	 */
	public static function mapCookies($val, $key)
	{
		if(preg_match('/^' . self::PREFIX . '(.*)/', $key))
		{
			return array($key, $val);
		}

		return false;
	}

	/**
	 * Init cookies and save them
	 */
	public static function initCookies()
	{
		// Get valid cookies
		$cookies = array_map('self::mapCookies', $_COOKIE,
			array_keys($_COOKIE));
		$cookies = array_filter($cookies);
		$cookies = array_values($cookies);

		// Get valid sessions
		$sessions = array_map('self::mapCookies', $_SESSION,
			array_keys($_SESSION));
		$sessions = array_filter($sessions);
		$sessions = array_values($sessions);

		// Sort valid cookies and create objects to save
		for($i = 0; $i < count($cookies); $i++)
		{
			$cookie = new NativeCookie;
			$cookie->setName($cookies[$i][0])
				   ->setContent($cookies[$i][1]);

			$expirationDate = explode(NativeCookie::$lifetimeSeperator,
				$cookies[$i][1]);

			if(array_key_exists(1, $expirationDate))
			{
				$cookie->setLifetime((int) ($expirationDate[1]));
			}
			else
			{
				$cookie->setLifetime(false);
			}

			self::$sessions[] = $cookie;
		}

		// Sort valid sessions and create objects to save
		for($i = 0; $i < count($sessions); $i++)
		{
			$session = new NativeSession;
			$session->setName($sessions[$i][0])
					->setContent($sessions[$i][1]);

			self::$sessions[] = $session;
		}
	}

	/**
	 * Adds a session to the session collection
	 *
	 * @param \Fewlines\Session\Cookie\Session $session
	 */
	private function addSession(\Fewlines\Session\Cookie\Session $session)
	{
		self::$sessions[] = $session;
	}

	/**
	 * Adds a cookie to the session collection
	 *
	 * @param FewlinesSessionCookieCookie $cookie
	 */
	private function addCookie(\Fewlines\Session\Cookie\Cookie $cookie)
	{
		self::$sessions[] = $cookie;
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

	/**
	 * Returns if the session was started
	 *
	 * @return boolean
	 */
	public static function isStarted()
	{
		return self::$isStarted;
	}

	/**
	 * Converts a name to a valid cookie
	 * name
	 *
	 * @param  string $name
	 * @return string
	 */
	public static function convertName($name)
	{
		return self::PREFIX . $name;
	}

	/**
	 * Creates a cookie
	 *
	 * @param string  $name
	 * @param *       $content
	 * @param int     $lifetime
	 * @param boolean $encrypt
	 */
	private function createCookie($name, $content, $lifetime, $encrypt, $path)
	{
		$name = self::convertName($name);

		$cookie = new NativeCookie;
		$cookie->setName($name)
			   ->setContent($content)
			   ->setLifetime($lifetime)
			   ->setPath($path)
			   ->setEncrypted($encrypt)
			   ->create();

		$this->addCookie($cookie);
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

		$name = self::convertName($name);

		$session = new NativeSession;
		$session->setName($name)
				->setContent($content)
				->create();

		$this->addSession($session);
	}

	/**
	 * Returns a saved session (cookie or session)
	 *
	 * @param  string $name
	 * @return *
	 */
	public static function get($name)
	{
		$name = self::convertName($name);
		$result = new Result;

		for($i = 0; $i < count(self::$sessions); $i++)
		{
			$session = self::$sessions[$i];

			if($session->getName() == $name)
			{
				if(get_class($session) == self::COOKIE_NAMESPACE)
				{
					$result->setCookie($session);
				}
				else if(get_class($session) == self::SESSION_NAMESPACE)
				{
					$result->setSession($session);
				}
			}
		}

		return $result;
	}
}