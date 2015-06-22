<?php
namespace Fewlines\Core\Template;

use Fewlines\Core\Template\Layout;
use Fewlines\Core\Helper\PathHelper;
use Fewlines\Core\Locale\Locale;
use Fewlines\Core\Application\Config;
use Fewlines\Core\Template\Template;
use Fewlines\Core\Http\Router;
use Fewlines\Core\Application\Registry;
use Fewlines\Core\Application\ProjectManager;

class Template extends Renderer
{
    /**
     * @var \Fewlines\Core\Template\Template
     */
    private static $instance;

    /**
     * The current layout
     *
     * @var \Fewlines\Core\Template\Layout
     */
    private $layout;

    /**
     * @var \Fewlines\Core\Template\View
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
     * @var \Fewlines\Core\Http\Router\Routes\Route
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
     * Holds the current instance of the
     * router created on startup
     *
     * @var \Fewlines\Core\Http\Router
     */
    private $router;

    /**
     * Sets the view and layout by the
     * given url parts
     *
     * @param array|\Fewlines\Core\Http\Router\Routes\Route $routeUrlParts
     */
    public function __construct($route) {
        parent::__construct();

        // Set instance so it can be use as singleton
        self::$instance = $this;

        // Check for route
        if(true == ($route instanceof \Fewlines\Core\Http\Router\Routes\Route)) {
            $this->activeRoute = $route;
        }
        else {
            $this->routeUrlParts = $route;
        }

        // Get default router
        $this->router = Router::getInstance();
    }

    /**
     * Returns the last created instance
     *
     * @return \Fewlines\Core\Template\Template
     */
    public static function getInstance() {
        if(true == is_null(self::$instance)) {
            return new self(Router::getInstance()->getRouteUrlParts());
        }

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
        if (false == is_null($index) && array_key_exists($index, $this->arguments)) {
            return $this->arguments[$index];
        }

        return $this->arguments;
    }

    /**
     * Sets the layout
     *
     * @param  string $layout
     * @param  string $view
     * @return self
     */
    public function setLayout($layout) {
        $project = ProjectManager::getActiveProject();
        $path = PathHelper::getRealPath(LAYOUT_PATH);

        if ($project && $layout != EXCEPTION_LAYOUT) {
            $path.= PathHelper::getRealPath($project->getId());
        }
        else {
            $path.= PathHelper::getRealPath(ProjectManager::getDefaultProject()->getId());
        }

        $path.= reset(explode(".", $layout)) . '.' . LAYOUT_FILETYPE;

        // Set layout
        $this->layout = new Layout($layout, $path);

        return $this;
    }

    /**
     * Returns if the current layout is
     * the layout of an exception
     *
     * @return boolean
     */
    private function isException() {
        if($this->layout instanceof \Fewlines\Core\Template\Layout) {
            return $this->layout->getName() == EXCEPTION_LAYOUT;
        }

        return false;
    }

    /**
     * @return self
     */
    public function setAutoView() {
        if(false == is_null($this->activeRoute) && ! $this->isException()) {
            $this->view = new View($this->activeRoute);
        }
        else {
            $view = $this->getRouteUrlPart('view');
            $action = $this->getRouteUrlPart('action');

            // Set explicit exception view
            if ($this->isException()) {
                $view = 'exception';
                $action = 'index';
            }

            // Create new view
            $this->view = new View(array(
                    'view' => $view,
                    'action' => $action
                ));
        }

        return $this;
    }

    /**
     * Create the view by a given
     * path
     *
     * @param  string $path
     * @return self
     */
    public function setView($path) {
        $this->view = new View($path);
        return $this;
    }

    /**
     * Returns the current layout object
     *
     * @return \Fewlines\Core\Template\Layout
     */
    public function getLayout() {
        return $this->layout;
    }

    /**
     * Returns the current view object
     *
     * @return \Fewlines\Core\Template\View
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
     * @return \Fewlines\Core\Xml\Element|false
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
     * @return \Fewlines\Core\Application\Environment
     */
    public function getEnvironment() {
        return Registry::get('environment');
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

        if ( ! is_null($controller) && property_exists($controller, $name)) {
            return $controller->{$name};
        }
        else if ( ! property_exists($this, $name)) {
            throw new Exception\PropertyNotFoundException(
                'Could not receive the property "' . $name . '".
				It does not exist.');
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
    public function __call($name, $args) {
        if (preg_match($this->viewHelperExp, $name)) {
            $helperName = preg_replace($this->viewHelperExp, '', $name);
            $defaultNs = ProjectManager::getDefaultProject()->getNsName() . VIEW_HELPER_RL_NS;
            $helperClass = $defaultNs . '\\' . $helperName;

            if ( ! class_exists($helperClass)) {
                throw new Exception\HelperNotFoundException(
                    'View helper "' . $helperClass . '" was
                    not found!'
                );
            }

            $helper = $this->getHelperClass($helperClass);

            if (false == ($helper instanceof \Fewlines\Core\Helper\AbstractViewHelper)) {
                throw new Exception\HelperInvalidInstanceException(
                    'The view helper "' . $helperName . '" was
			 		NOT extended by "' . $defaultNs . '\AbstractViewHelper"'
                );
            }

            if (false == method_exists($helper, $helperName)) {
                throw new Exception\HelperMethodNotFoundException(
                    'The view helper method "' . $helperName . '"
					was not found!'
                );
            }

            $reflection = new \ReflectionMethod($helperClass, $helperName);
            $needArgsCount = $reflection->getNumberOfRequiredParameters();
            $foundArgsCount = count($args);

            if ($needArgsCount > $foundArgsCount) {
                throw new Exception\HelperArgumentException(
                    'The view helper method "' . $helperName . '"
    				requires at least "' . $needArgsCount . '"
    				parameter(s). Found ' . $foundArgsCount
                );
            }

            return call_user_func_array(array($helper, $helperName), $args);
        }
        else {
            $controller = $this->view->getViewController();

            if ( ! is_null($controller) && method_exists($controller, $name)) {
                return call_user_func_array(array($controller, $name), $args);
            }
            else if ( ! method_exists($this, $name)) {
                $msg = 'The method "' . $name . '" was not found in ' . get_class($this);

                if ( ! is_null($controller)) {
                    $msg.= ' or in the controller ' . get_class($controller);
                }

                throw new Exception\TemplateMethodNotFoundException($msg);
            }

            return call_user_func_array(array($this, $name), $args);
        }
    }
}