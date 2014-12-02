<?php
/**
 * fewlines CMS
 *
 * Description: The layout which extends with the view
 *
 * @copyright Copyright (c) fewlines
 * @author Davide Perozzi
 */

namespace Fewlines\Template;
use Fewlines\Template\View as View;

class Layout extends View
{
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
	 * @param string $path
	 * @param array  $routeUrlParts
	 * @param \Fewlines\Template\Template $tpl
	 */
	public function __construct($path, $routeUrlParts, \Fewlines\Template\Template $tpl)
	{
		$this->layoutPath = $path;
		$this->template = $tpl;

		$this->setView($routeUrlParts['view'], $routeUrlParts['action']);
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
}

?>