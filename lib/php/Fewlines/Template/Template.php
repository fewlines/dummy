<?php
namespace Fewlines\Template;

use Fewlines\Template\Layout;
use Fewlines\Helper\PathHelper;
use Fewlines\Locale\Locale;
use Fewlines\Application\Config;
use Fewlines\Template\Template;
use Fewlines\Http\Request as HttpRequest;

class Template extends Renderer
{
    /**
     * @var \Fewlines\Template\Template
     */
    private static $instance;

    /**
     * The current layout
     *
     * @var \Fewlines\Template\Layout
     */
    private $layout;

    /**
     * @var \Fewlines\Template\View
     */
    private $view;

    /**
     * Holds the url parts parsed from the
     * router
     *
     * @var array
     */
    private $routeUrlParts;

    /**
     * @var \Fewlines\Http\Router\Routes\Route
     */
    private $activeRoute;

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
     * @param array|\Fewlines\Http\Router\Routes\Route $routeUrlParts
     */
    public function __construct($route) {
        // Set instance so it can be use as singlteon
        self::$instance = $this;

        // Check for route
        if(true == ($route instanceof \Fewlines\Http\Router\Routes\Route)) {
            $this->activeRoute = $route;
        }
        else {
            // Init layout
            $this->routeUrlParts = $route;
        }

        $this->setLayout(DEFAULT_LAYOUT);

        // Create renderer
        $this->renderer = new Renderer();
    }

    /**
     * Returns the last created instance
     *
     * @return \Fewlines\Template\Template
     */
    public static function getInstance() {
        return self::$instance;
    }

    /**
     * Gets a property from the route url parts
     *
     * @param  string $key
     * @return string
     */
    public function getRouteUrlPart($key) {
        return $this->routeUrlParts[$key];
    }

    /**
     * Gets an helper instance and caches it
     *
     * @param  string $helperClass
     * @return *
     */
    protected function getHelperClass($helperClass) {
        foreach ($this->cachedHelpers as $class => $instance) {
            if (preg_match('/' . str_replace('\\', '\\\\', $helperClass) . '/i', $class)) {
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
    private function cacheHelper($instance) {
        $this->cachedHelpers[get_class($instance)] = $instance;
        $instance->init();

        return $instance;
    }

    /**
     * Renders the current template
     *
     * @param array|* $args
     */
    public function renderAll($args = array()) {
        if (true == is_array($args)) {
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
    public function setArguments($args) {
        $this->arguments = $args;
    }

    /**
     * Gets the arguments parsed
     *
     * @param  null|integer $index
     * @return array|*
     */
    public function getArguments($index = null) {
        if (false == is_null($index)) {
            return $this->arguments[$index];
        }

        return $this->arguments;
    }

    /**
     * Sets the layout
     *
     * @param string $layout
     */
    public function setLayout($layout) {
        $path = PathHelper::getRealPath(LAYOUT_PATH);
        $path = $path . reset(explode(".", $layout)) . '.' . LAYOUT_FILETYPE;

        $this->layout = new Layout($layout, $path);

        // Set the new view
        $this->setView();
    }

    public function setView() {
        if(false == is_null($this->activeRoute)) {
            $this->view = new View($this->activeRoute);
        }
        else {
            $view = $this->getRouteUrlPart('view');
            $action = $this->getRouteUrlPart('action');

            // Set exception layout
            if ($this->layout->getName() == EXCEPTION_LAYOUT) {
                $view = $httpRequest->getDefaultDestination('view');
                $action = $httpRequest->getDefaultDestination('action');
            }

            // Create view
            $this->view = new View(array(
                    'view' => $view,
                    'action' => $action
                ));
        }
    }

    /**
     * Returns the current layout object
     *
     * @return \Fewlines\Template\Layout
     */
    public function getLayout() {
        return $this->layout;
    }

    /**
     * Returns the current view object
     *
     * @return \Fewlines\Template\View
     */
    public function getView() {
        return $this->view;
    }

    /**
     * Translates a path to a translation
     * string
     *
     * @param  string $path
     * @return string
     */
    protected function translate($path) {
        return Locale::get($path);
    }

    /**
     * Gets a config element by a given
     * path
     *
     * @param  string $path
     * @return \Fewlines\Xml\Element|false
     */
    protected function getConfig($path) {
        return Config::getInstance()->getElementByPath($path);
    }

    /**
     * Gets config elements from a element
     *
     * @param  string $path
     * @return array
     */
    protected function getConfigs($path) {
        return Config::getInstance()->getElementsByPath($path);
    }

    /**
     * Returns the content of
     * the rendered element
     *
     * @param  string $viewPath
     * @param  array  $config
     * @param  string $wrapper
     * @return string
     */
    public function render($viewPath, $config = array(), $wrapper = '') {
        $bckt = debug_backtrace();
        $view = PathHelper::getRealViewPath(ltrim($viewPath, '/'));
        $file = $bckt[0]['file'];
        $dir = pathinfo($file, PATHINFO_DIRNAME);

        // Handle relative path
        if (false == PathHelper::isAbsolute($viewPath)) {
            $path = PathHelper::getRealViewFile(PathHelper::normalizePath($dir . '/' . $viewPath));
            $view = PathHelper::normalizePath(realpath($path));
        }

        if (false == $view || false == file_exists($view)) {
            if (false == $view) {
                $view = $path;
            }

            throw new Exception\ViewIncludeNotFoundException("
					The view \"" . $view . "\" was not found
					and could not be included
				");
        }

        $content = $this->getRenderedHtml($view, $config);

        if (false == empty($wrapper)) {
            $content = sprintf($wrapper, $content);
        }

        return $content;
    }

    /**
     * Render a component and outputs
     * the content of it
     *
     * @param  string $viewPath
     * @param  array  $config
     * @param  string $wrapper
     * @return string
     */
    public function insert($viewPath, $config = array(), $wrapper = '') {
        echo $this->render($viewPath, $config, $wrapper);
    }


    ###########################
    ##### MAGIC FUNCTIONS #####
    ###########################


    /**
     * Handles an extern var set
     *
     * @param string $name
     * @param *		 $content
     */
    public function __set($name, $content) {
        $this->$name = $content;
    }

    /**
     * Handles all get requests
     *
     * @param  string $name
     * @return *
     */
    public function __get($name) {
        $controller = $this->view->getViewController();

        if (false == is_null($controller) && true == property_exists($controller, $name)) {
            return $controller->{$name};
        }
        else if (false == property_exists($this, $name)) {
            throw new Exception\PropertyNotFoundException("Could not receive the property \"" . $name . "\".
				It does not exist.");
        }

        return $this->{$name};
    }

    /**
     * Calls undefined functions
     * (mostly used for view helpers)
     *
     * @param  string $name
     * @param  array  $value
     * @return *
     */
    public function __call($name, $args) {
        if (true == preg_match($this->viewHelperExp, $name)) {
            $helperName = preg_replace($this->viewHelperExp, '', $name);
            $helperClass = 'Fewlines\Helper\View\\' . $helperName;

            if (false == class_exists($helperClass)) {
                throw new Exception\HelperNotFoundException("View helper \"" . $helperClass . "\"
					was not found!");
            }

            $helper = $this->getHelperClass($helperClass);

            if (false == ($helper instanceof \Fewlines\Helper\AbstractViewHelper)) {
                throw new Exception\HelperInvalidInstanceException("The view helper \"" . $helperName . "\" was
			 		NOT extended by \Fewlines\Helper\AbstractViewHelper");
            }

            if (false == method_exists($helper, $helperName)) {
                throw new Exception\HelperMethodNotFoundException("The view helper method \"" . $helperName . "\"
					was not found!");
            }

            $reflection = new \ReflectionMethod($helperClass, $helperName);
            $needArgsCount = $reflection->getNumberOfRequiredParameters();
            $foundArgsCount = count($args);

            if ($needArgsCount > $foundArgsCount) {
                throw new Exception\HelperArgumentException("The view helper method \"" . $helperName . "\"
    				requires at least " . $needArgsCount . "
    				parameter(s). Found " . $foundArgsCount);
            }

            return call_user_func_array(array($helper, $helperName), $args);
        }
        else {
            $controller = $this->view->getViewController();

            if (false == is_null($controller) && true == method_exists($controller, $name)) {
                return call_user_func_array(array($controller, $name), $args);
            }
            else if (false == method_exists($this, $name)) {
                $msg = "The method \"" . $name . "\" was not found in " . get_class($this);

                if (false == is_null($controller)) {
                    $msg.= " or in the controller . " . get_class($controller);
                }

                throw new Exception\TemplateMethodNotFoundException($msg);
            }

            return call_user_func_array(array($this, $name), $args);
        }
    }
}
