<?php

namespace Fewlines\Template;

use Fewlines\Template\Layout;
use Fewlines\Template\Renderer;
use Fewlines\Helper\PathHelper;
use Fewlines\Locale\Locale;
use Fewlines\Application\Config;

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
	 * The arguments of the template parsed
	 * from the outside of the template
	 *
	 * @var array
	 */
	public $arguments = array();

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

		// Init caller
		parent::initCaller($this->layout);

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
	 *
	 * @param array|* $args
	 */
	public function render($args = array())
	{
		if(true == is_array($args))
		{
			$this->setArguments($args);
		}

		$this->renderLayout();
	}

	/**
	 * Sets the argument defined from
	 * outside of the application
	 *
	 * @param array $args
	 */
	public function setArguments($args)
	{
		$this->arguments = $args;
	}

	/**
	 * Gets the arguments parsed
	 *
	 * @param  null|integer $index
	 * @return array|*
	 */
	public function getArguments($index = null)
	{
		if(false == is_null($index))
		{
			return $this->arguments[$index];
		}

		return $this->arguments;
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
	 * Translates a path to a translation
	 * string
	 *
	 * @param  string $path
	 * @return string
	 */
	protected function translate($path)
	{
		return Locale::get($path);
	}

	/**
	 * Gets a config element by a given
	 * path
	 *
	 * @param  string $path
	 * @return \Fewlines\Xml\Element|false
	 */
	protected function getConfig($path)
	{
		return Config::getInstance()->getElementByPath($path);
	}

	/**
	 * Gets config elements from a element
	 *
	 * @param  string $path
	 * @return array
	 */
	protected function getConfigs($path)
	{
		return Config::getInstance()->getElementsByPath($path);
	}

	/**
	 * Includes a component/view inside a view.
	 *
	 * @param  string $viewPath
	 * @param  array  $config
	 * @param  string $wrapper
	 * @return string
	 */
	public function insert($viewPath, $config = array(), $wrapper = '')
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

		$content = $this->getRenderedHtml($view, $config);

		if(false == empty($wrapper))
		{
			$content = sprintf($wrapper, $content);
		}

		return $content;
	}
}