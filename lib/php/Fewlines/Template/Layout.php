<?php

namespace Fewlines\Template;

use Fewlines\Template\View;
use Fewlines\Http\Request as HttpRequest;

class Layout extends View
{
	/**
	 * The name of the layout
	 *
	 * @var string
	 */
	private $layoutName;

	/**
	 * Holds the path to the layout
	 *
	 * @var string
	 */
	private $layoutPath;

	/**
	 * Holds the current template instance
	 *
	 * @var \Fewlines\Template\Template
	 */
	protected $template;

	/**
	 * @param string $name
	 * @param string $path
	 * @param array  $routeUrlParts
	 * @param \Fewlines\Template\Template $template
	 */
	public function __construct($name, $path, $routeUrlParts, \Fewlines\Template\Template $template)
	{
		$this->layoutName = $name;
		$this->layoutPath = $path;
		$this->template   = $template;

		// Set exception layout
		if($this->getLayoutName() == EXCEPTION_LAYOUT)
		{
			$httpRequest = HttpRequest::getInstance();

			$routeUrlParts['view']   = $httpRequest->getDefaultDestination('view');
			$routeUrlParts['action'] = $httpRequest->getDefaultDestination('action');
		}

		$this->setView($routeUrlParts['view'], $routeUrlParts['action'], $this);
	}

	/**
	 * Returns the path to the layout
	 *
	 * @return string
	 */
	public function getLayoutPath()
	{
		return $this->layoutPath;
	}

	/**
	 * Gets the name of the layout
	 *
	 * @return string
	 */
	public function getLayoutName()
	{
		return $this->layoutName;
	}
}