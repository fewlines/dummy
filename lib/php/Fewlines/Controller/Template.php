<?php

namespace Fewlines\Controller;

use Fewlines\Helper\UrlHelper;
use Fewlines\Application\Config;
use Fewlines\Http\Request as HttpRequest;
use Fewlines\Http\Header as HttpHeader;

class Template implements TemplateInterface
{
	/**
	 * Holds the whole template instance
	 *
	 * @var \Fewlines\Template\Template
	 */
	protected $template;

	/**
	 * Assigns a var to template
	 *
	 * @param  string $name
	 * @param  *	  $content
	 * @return *
	 */
	protected function assign($name, $content)
	{
		if(property_exists($this->template, $name))
		{
			throw new Exception\PropertyExistException(
				"Could not assign the variable
				\"" . $name . "\". The property
				already exists."
			);
		}

		$this->template->$name = $content;

		return $content;
	}

	/**
	 * Inits with the template
	 *
	 * @param  \Fewlines\Template\Template $template
	 */
	public function init(\Fewlines\Template\Template $template)
	{
		$this->template    = $template;
		$this->httpRequest = httpRequest::getInstance();
	}

	/**
	 * Get the instantiated config instance
	 *
	 * @return \Fewlines\Application\Config
	 */
	protected function getConfig()
	{
		return Config::getInstance();
	}

	/**
	 * Redirects
	 *
	 * @param string $url
	 */
	protected function redirect($url)
	{
		HttpHeader::redirect($url);
	}

	/**
	 * Returns the base url
	 *
	 * @param  string|array $parts
	 * @return string
	 */
	protected function getBaseUrl($parts = "")
	{
		return UrlHelper::getBaseUrl($parts);
	}
}