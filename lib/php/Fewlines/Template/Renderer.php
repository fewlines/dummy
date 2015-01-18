<?php
/**
 * fewlines CMS
 *
 * Description: A simple html renderer for the template
 * using new buffer
 *
 * Note: Should only be extended by \Fewlines\Template\Template
 *
 * @copyright Copyright (c) fewlines
 * @author Davide Perozzi
 */

namespace Fewlines\Template;

use Fewlines\Http\Header as HttpHeader;
use Fewlines\Helper\PathHelper;

class Renderer
{
	/**
	 * Holds the current layout instance
	 *
	 * @var \Fewlines\Template\Layout
	 */
	private $layout;

	/**
	 * Init function (called from child)
	 */
	public function initLayout()
	{
		$this->layout = $this->getLayout();
	}

	/**
	 * Includes a php file and returns the content
	 * output with a buffer
	 *
	 * @param  string $file
	 * @return string
	 */
	protected function getRenderedHtml($file)
	{
		ob_start();

		include $file;

		$html = ob_get_contents();

		ob_clean();
		ob_end_flush();

		return $html;
	}

	protected function renderLayout()
	{
		// Update layout
		$this->initLayout();

		$file = $this->layout->getLayoutPath();
		echo $this->getRenderedHtml($file);
	}

	protected function renderView()
	{
		// Get view and action from request
		$view   = $this->getRouteUrlPart('view');
		$action = $this->getRouteUrlPart('action');

		// Get view and action
		$file   = $this->layout->getViewPath();
		$action = $this->layout->getViewAction();

		// Call controller from view (if exists)
		$this->layout->initViewController();

		// Output rendered html
		echo $this->getRenderedHtml($file);
	}
}

?>