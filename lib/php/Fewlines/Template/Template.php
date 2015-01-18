<?php
/**
 * fewlines CMS
 *
 * Description: Template management
 *
 * @copyright Copyright (c) fewlines
 * @author Davide Perozzi
 */

namespace Fewlines\Template;

use Fewlines\Template\Layout;
use Fewlines\Template\Renderer;
use Fewlines\Helper\PathHelper;

class Template extends Caller
{
	/**
	 * The current layout
	 *
	 * @var \Fewlines\Template\Layout
	 */
	private $layout;

	/**
	 * Holds the url parts parsed from the
	 * router
	 *
	 * @var array
	 */
	private $routeUrlParts;

	/**
	 * Holds the instances of the used
	 * helpers
	 *
	 * @var array
	 */
	private $cachedHelpers = array();

	/**
	 * Expression to filter view helpers
	 * called from the view with this
	 * keywords
	 *
	 * @var string
	 */
	public $viewHelperExp = '/helper|Helper/';

	/**
	 * Sets the view and layout by the
	 * given url parts
	 *
	 * @param array $routeUrlParts
	 */
	public function __construct($routeUrlParts)
	{
		$this->routeUrlParts = $routeUrlParts;
		$this->setLayout(DEFAULT_LAYOUT);

		// Init the renderer
		parent::initLayout();
	}

	/**
	 * Handles an extern var set
	 *
	 * @param string $name
	 * @param *		 $content
	 */
	public function __set($name, $content)
	{
		$this->$name = $content;
	}

	/**
	 * Gets a property from the route url parts
	 *
	 * @param  string $key
	 * @return string
	 */
	public function getRouteUrlPart($key)
	{
		return $this->routeUrlParts[$key];
	}

	/**
	 * Gets an helper instance and caches it
	 *
	 * @param  string $helperClass
	 * @return *
	 */
	protected function getHelperClass($helperClass)
	{
		foreach($this->cachedHelpers as $class => $instance)
		{
			if(preg_match('/' . str_replace('\\', '\\\\', $helperClass) . '/i', $class))
			{
				return $instance;
				break;
			}
		}

		return $this->cacheHelper(new $helperClass);
	}

	/**
	 * Caches one helper
	 *
	 * @param  * $instance
	 * @return * Returns the given instance
	 */
	private function cacheHelper($instance)
	{
		$this->cachedHelpers[get_class($instance)] = $instance;
		$instance->init();

		return $instance;
	}

	/**
	 * Renders the current template
	 */
	public function render()
	{
		$this->renderLayout();
	}

	/**
	 * Sets the layout
	 */
	public function setLayout($layout)
	{
		$path = PathHelper::getRealPath(LAYOUT_PATH);
		$path = $path . reset(explode(".", $layout)) . '.' . LAYOUT_FILETYPE;

		$this->layout = new Layout($layout, $path, $this->routeUrlParts, $this);
	}

	/**
	 * Returns the current layout object
	 *
	 * @return \Fewlines\Template\Layout
	 */
	public function getLayout()
	{
		return $this->layout;
	}

	/**
	 * Includes a view inside a view.
	 * Mostly called in views.
	 *
	 * @return string
	 */
	public function includeView($viewPath, $wrapper = '')
	{
		$bckt = debug_backtrace();
		$view = PathHelper::getRealViewPath(ltrim($viewPath, '/'));
		$file = $bckt[0]['file'];
		$dir  = pathinfo($file, PATHINFO_DIRNAME);

		// Handle relative path
		if(false == PathHelper::isAbsolute($viewPath))
		{
			$path = PathHelper::getRealViewFile(PathHelper::normalizePath($dir . '/' . $viewPath));
			$view = PathHelper::normalizePath(realpath($path));
		}

		if(false == $view || false == file_exists($view))
		{
			if(false == $view)
			{
				$view = $path;
			}

			throw new Exception\ViewIncludeNotFoundException("
					The view \"" . $view . "\" was not found
					and could not be included
				");
		}

		$content = $this->getRenderedHtml($view);

		if(false == empty($wrapper))
		{
			$content = sprintf($wrapper, $content);
		}

		return $content;
	}
}

?>