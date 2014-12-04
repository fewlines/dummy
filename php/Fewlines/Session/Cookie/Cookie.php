<?php
/**
 * fewlines CMS
 *
 * Description: The native cookie object
 *
 * @copyright Copyright (c) fewlines
 * @author Davide Perozzi
 */

namespace Fewlines\Session\Cookie;

class Cookie
{
	public static $lifetimeSeperator = '[EXPD]:';

	/**
	 * Name of the cookie
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Value of the cookie
	 *
	 * @var string
	 */
	protected $content;

	/**
	 * The lifetime in seconds
	 *
	 * @var integer|boolean
	 */
	private $lifetime = 0;

	/**
	 * The path where the cookie will be set
	 *
	 * @var string
	 */
	private $path;

	/**
	 * Is encrypted cookie or not
	 *
	 * @var boolean
	 */
	private $isEncrypted = false;

	/**
	 * Deletes the cookie
	 */
	public function delete()
	{
		setcookie($this->name, '', -1);
	}

	/**
	 * Sets the cookie
	 */
	public function create()
	{
		if(true == $this->isEncrypted)
		{
			/**
			 * @TODO: Write encryption/decryption system
			 */
		}

		$content = $this->content . self::$lifetimeSeperator . $this->lifetime;

		// Set the cookie
		setcookie($this->name, $content, $this->lifetime, $this->path);
	}

	/**
	 * Sets if the content of the cookie should be encrypted or not
	 *
	 * @param boolean $isEncrypted
	 * @return \Fewlines\Session\Cookie
	 */
	public function setEncrypted($isEncrypted)
	{
		$this->isEncrypted = $isEncrypted;
		return $this;
	}

	/**
	 * Sets the path to the cookie
	 *
	 * @param string $path
	 * @return \Fewlines\Session\Cookie
	 */
	public function setPath($path)
	{
		$this->path = $path;
		return $this;
	}

	/**
	 * Returns the lifetime in
	 * seconds
	 *
	 * @return integer|boolean
	 */
	public function getLifetime()
	{
		return $this->lifetime;
	}

	/**
	 * Sets the lifetime of the cookie
	 * in seconds
	 *
	 * @param integer|boolean $lifetime
	 * @return \Fewlines\Session\Cookie
	 */
	public function setLifetime($lifetime)
	{
		$this->lifetime = $lifetime;
		return $this;
	}

	/**
	 * Sets the name
	 *
	 * @param string $name
	 * @return \Fewlines\Session\Cookie
	 */
	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * Sets the content
	 *
	 * @param string $content
	 * @return \Fewlines\Session\Cookie
	 */
	public function setContent($content)
	{
		$this->content = $content;
		return $this;
	}

	/**
	 * Returns the name of the cookie
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Returns the content of
	 * the cookie
	 *
	 * @return string
	 */
	public function getContent()
	{
		$content = explode(self::$lifetimeSeperator, $this->content);
		return $content[0];
	}
}

?>