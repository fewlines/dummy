<?php
/**
 * fewlines CMS
 *
 * Description: The controller for
 * all views (holds standard functions)
 *
 * @copyright Copyright (c) fewlines
 * @author Davide Perozzi
 */

namespace Fewlines\Controller;

use Fewlines\Application\Config;
use Fewlines\Controller\TemplateInterface;
use Fewlines\Http\Request as HttpRequest;

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
		$this->template = $template;
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
}

?>