<?php

namespace Fewlines\Session;

use Fewlines\Session\Void;

class Result
{
	/**
	 * Holds a cookie as result
	 *
	 * @var \Fewlines\Session\Cookie\Session
	 */
	private $session;

	/**
	 * Holds a cookie as result
	 *
	 * @var \Fewlines\Session\Cookie\Cookie
	 */
	private $cookie;

	/**
	 * Sets a session (native) as result
	 *
	 * @param $session \Fewlines\Session\Cookie\Session
	 */
	public function setSession(\Fewlines\Session\Cookie\Session $session)
	{
		$this->session = $session;
	}

	/**
	 * Sets a cookie (native) as result
	 *
	 * @param $session \Fewlines\Session\Cookie\Cookie
	 */
	public function setCookie(\Fewlines\Session\Cookie\Cookie $cookie)
	{
		$this->cookie = $cookie;
	}

	/**
	 * Resturns the session (native) if
	 * a session was set
	 *
	 * @return \Fewlines\Session\Cookie\Session
	 */
	public function getSession()
	{
		return is_null($this->session) ? new Void : $this->session;
	}

	/**
	 * Returns the cookie (native) if
	 * a cookie was set
	 *
	 * @return \Fewlines\Session\Cookie\Cookie
	 */
	public function getCookie()
	{
		return is_null($this->cookie) ? new Void : $this->cookie;
	}

	/**
	 * Check if a cookie is given
	 *
	 * @return boolean
	 */
	public function isCookie()
	{
		return !is_null($this->cookie);
	}

	/**
	 * Check if a session is given
	 *
	 * @return boolean
	 */
	public function isSession()
	{
		return !is_null($this->session);
	}

	/**
	 * Returns if any result was set
	 *
	 * @return boolean
	 */
	public function isEmpty()
	{
		return is_null($this->cookie) && is_null($this->session);
	}
}