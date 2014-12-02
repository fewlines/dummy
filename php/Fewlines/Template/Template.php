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

use Fewlines\Template\Layout as Layout;
use Fewlines\Template\Renderer as Renderer;
use Fewlines\Helper\PathHelper as PathHelper;

class Template extends Renderer
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
		$this->setLayout();

		// Init the renderer
		parent::init();
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
	 * Handles all get requests
	 *
	 * @param  string $name
	 * @return *
	 */
	public function __get($name)
	{
		if(!property_exists($this, $name))
		{
			throw new Exception\PropertyNotFoundException(
				"Could not receive the property \"" . $name . "\".
				It does not exist."
			);
		}

		return $this->$name;
	}

	/**
	 * Calls undefined functions
	 * (mostly used for view helpers)
	 *
	 * @param  string $name
	 * @param  array  $value
	 * @return *
	 */
	public function __call($name, $args)
	{
		if(preg_match($this->viewHelperExp, $name))
		{
			$helperName = preg_replace($this->viewHelperExp, '', $name);
			$helperClass = 'Fewlines\Helper\View\\' . $helperName;

			if(!class_exists($helperClass))
			{
				throw new Exception\HelperNotFoundException(
					"View helper \"" . $helperClass . "\"
					was not found!"
				);
			}

			$helper = $this->getHelperClass($helperClass);

			if(false == ($helper instanceof \Fewlines\Helper\Viewhelper))
			{
			 	throw new Exception\HelperInvalidInstanceException(
			 		"The view helper \"" . $helperName . "\" was
			 		NOT extended by \Fewlines\Helper\Viewhelper"
			 	);
			}

			if(!method_exists($helper, $helperName))
			{
				throw new Exception\HelperMethodNotFoundException(
					"The view helper method \"" . $helperName . "\"
					was not found!"
				);
			}

			$reflection = new \ReflectionMethod($helperClass, $helperName);
    		$needArgsCount = $reflection->getNumberOfRequiredParameters();
    		$foundArgsCount = count($args);

    		if($needArgsCount > $foundArgsCount)
    		{
    			throw new Exception\HelperArgumentException(
    				"The view helper method \"" . $helperName ."\"
    				requires at least " . $needArgsCount . "
    				parameter(s). Found " . $foundArgsCount
    			);
    		}

    		return call_user_func_array(array($helper, $helperName), $args);
		}
		else
		{
			if(!method_exists($this, $name))
			{
				throw new Exception\TemplateMethodNotFoundException(
					"The method \"" . $name . "\" was not found in
					" . get_class($this)
				);
			}

			return call_user_func_array(array($this, $name), $args);
		}
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
	public function setLayout()
	{
		$path = PathHelper::getRealPath(LAYOUT_PATH);
		$path = $path . DEFAULT_LAYOUT . '.' . LAYOUT_FILETYPE;
		$this->layout = new Layout($path, $this->routeUrlParts, $this);
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
}

?>