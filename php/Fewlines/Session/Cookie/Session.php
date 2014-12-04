<?php
/**
 * fewlines CMS
 *
 * Description: The native session object
 *
 * @copyright Copyright (c) fewlines
 * @author Davide Perozzi
 */

namespace Fewlines\Session\Cookie;

class Session extends Cookie
{
	/**
	 * Deletes the session
	 */
	public function delete()
	{
		if(array_key_exists($this->name, $_SESSION))
		{
			unset($_SESSION[$this->name]);
		}
	}

	/**
	 * Sessions do not have a defined lifetime
	 *
	 * @return boolean always false
	 */
	public function getLifetime()
	{
		return false;
	}

	/**
	 * Set the session
	 */
	public function create()
	{
		$_SESSION[$this->name] = $this->content;
	}
}

?>