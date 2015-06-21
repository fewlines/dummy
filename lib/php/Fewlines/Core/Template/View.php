<?php
namespace Fewlines\Core\Template;

use Fewlines\Core\Template\Template;
use Fewlines\Core\Helper\PathHelper;
use Fewlines\Core\Helper\NamespaceHelper;
use Fewlines\Core\Application\Registry;
use Fewlines\Core\Application\ProjectManager;
use Fewlines\Core\Http\Header as HttpHeader;
use Fewlines\Core\Http\Router;

class View
{
    /**
     * @var string
     */
    const ACTION_POSTFIX = 'Action';

    /**
     * The name of the view (could be overwritten
     * by anything e.g. a 404 error)
     *
     * @var string
     */
    private $name;

    /**
     * The real viewname as in the url
     *
     * @var string
     */
    private $realName;

    /**
     * Current action
     *
     * @var string
     */
    private $action;

    /**
     * The filename of the view templates
     *
     * @var string
     */
    private $path;

    /**
     * Controller class of the current view
     *
     * @var string
     */
    private $controllerClass;

    /**
     * Enables when a route is given instead
     * of the default method
     *
     * @var \Fewlines\Core\Http\Router\Routes\Route
     */
    private $activeRoute;

    /**
     * Controller instance of the current view
     *
     * @var \Fewlines\Core\Controller\View
     */
    public $viewController;

    /**
     * Controller of the route
     *
     * @var \Fewlines\Core\Controller\View
     */
    private $routeController;

    /**
     * Determinates if the controller returns
     * an empty string - no matter if called
     * from a view or an route
     *
     * @var boolean
     */
    private $disableController = false;

    /**
     * Returns the name of the rendered view
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Gets the name of the first view
     * (no overwrites)
     *
     * @return string
     */
    public function getRealName() {
        return $this->realName;
    }

    /**
     * Returns the view action
     *
     * @return string
     */
    public function getAction() {
        return $this->action;
    }

    /**
     * Sets the view action
     *
     * @param string $action
     */
    public function setAction($action) {
        $this->action = $action;
    }

    /**
     * Returns the path to the current view
     *
     * @return string
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * Sets the view path
     *
     * @param string $view
     */
    public function setPath($view) {
        $layout = Template::getInstance()->getLayout()->getName();
        $viewFile = PathHelper::getRealViewPath($view, $this->getAction(), $layout);

        if ( ! file_exists($viewFile)) {
            HttpHeader::set(404);
        }

        $this->path = $viewFile;
    }

    /**
     * Init the view with some options
     * called from the layout
     *
     * @param array $config
     */
    public function __construct($config) {
        if ( ! is_array($config) && preg_match('/\//', $config)) {
            $this->setPath($config);
            $this->disableController = true;
        }
        // Set components by default layout
        else if (is_array($config) && array_key_exists('view', $config) && array_key_exists('action', $config)) {
            $this->setAction($config['action']);
            $this->setName($config['view']);
            $this->setPath($config['view']);
            $this->setViewControllerClass();
        }
        else if (true == ($config instanceof \Fewlines\Core\Http\Router\Routes\Route)) {
            $this->activeRoute = $config;
            $this->setRouteControllerClass($this->activeRoute->getToClass());
        }
        else {
            $this->disableController = true;
        }
    }

    /**
     * Sets the name of the view
     *
     * @param string $name
     */
    private function setName($name) {
        if (true == is_null($this->realName)) {
            $this->realName = $name;
        }

        $this->name = $name;
    }

    /**
     * @return boolean
     */
    public function isRouteActive() {
        return false == is_null($this->activeRoute);
    }

    /**
     * Sets the controller by the active namespace
     */
    private function setViewControllerClass() {
        $project = ProjectManager::getActiveProject();
        $class = '\\';

        if ($project && $project->hasNsName() && Template::getInstance()->getLayout()->getName() != EXCEPTION_LAYOUT) {
            $class.= $project->getNsName();
        }
        else {
            $class.= ProjectManager::getDefaultProject()->getNsName();
        }

        $class.= CONTROLLER_V_RL_NS . '\\' . ucfirst($this->name);

        if ( ! class_exists($class)) {
            throw new View\Exception\ControllerClassNotFoundException(
                'The class "' . $class . '", for the
                controller was not found.');
        }

        $this->controllerClass = $class;
    }

    /**
     * Set the controller
     * for the route
     *
     * @param string $class
     */
    public function setRouteControllerClass($class) {
        $method = strtolower(Router::getInstance()->getRequest()->getHttpMethod());
        $routeMethod = strtolower($this->activeRoute->getType());

        if ($routeMethod == 'any' || $method == $routeMethod) {
            if (true == class_exists($class)) {
                $this->controllerClass = $class;
            }
            else {
                throw new View\Exception\ControllerClassNotFoundException(
                    'The class "' . $this->controllerClass . '" for the
                    controller was not found.'
                );
            }
        }
        else {
            if(Registry::get('environment')->isLocal()) {
                throw new View\Exception\InvalidHttpMethodException(
                    'Invalid HTTP method found'
                );
            }
            else {
                HttpHeader::set(404);
            }
        }
    }

    /**
     * Returns the instantiated view
     * controller if exists
     *
     * @return \Fewlines\Core\Controller\View
     */
    public function getViewController() {
        return $this->viewController;
    }

    /**
     * Returns the instantiated route
     * controller if exists
     *
     * @return \Fewlines\Core\Controller\View
     */
    public function getRouteController() {
        return $this->routeController;
    }

    /**
     * Inits the active controller
     * (view or route)
     *
     * @return null|*
     */
    public function initController() {
        if (true == $this->disableController) {
            return false;
        }

        if (false == is_null($this->activeRoute)) {
            return $this->initRouteController();
        }

        return $this->initViewController();
    }

    /**
     * Init the controller of the current view
     * (if exists)
     *
     * @return null|*
     */
    public function initViewController() {
        $this->viewController = new $this->controllerClass;

        if (true == ($this->viewController instanceof \Fewlines\Core\Controller\View)) {
            $this->viewController->init(Template::getInstance());
            return $this->callViewAction($this->getAction() . self::ACTION_POSTFIX);
        }
        else {
            throw new View\Exception\ControllerInitialisationGoneWrongException(
                'The view controller could not be initialized.
                Must be instance of \Fewlines\Core\Controller\View'
            );
        }

        return null;
    }

    /**
     * Init the controller of a route
     *
     * @return null|*
     */
    public function initRouteController() {
        $this->routeController = new $this->controllerClass;

        if (true == ($this->routeController instanceof \Fewlines\Core\Controller\View)) {
            $this->routeController->init(Template::getInstance());
            return $this->callRouteMethod($this->activeRoute->getToMethod());
        }
        else {
            throw new View\Exception\ControllerInitialisationGoneWrongException(
                'The route controller could not be initialized.
                Must be instance of \Fewlines\Controller\View'
            );
        }

        return null;
    }

    /**
     * Calls the action of the
     * current controller
     *
     * @param string $method
     * @return *
     */
    private function callViewAction($method) {
        if (false == method_exists($this->viewController, $method)) {
            throw new View\Exception\ActionNotFoundException('Could not found the action (method) "' . $method . '"
                - Check the controller "' . $this->controllerClass . '"
                for it');
        }

        return $this->viewController->{$method}();
    }

    /**
     * Calls the method
     * of the current route
     * controller
     *
     * @param string $method
     * @return *
     */
    private function callRouteMethod($method) {
        if (false == method_exists($this->routeController, $method)) {
            throw new View\Exception\MethodNotFoundException('Could not found the method "' . $method . '"
                - Check the controller "' . $this->controllerClass . '"
                for it');
        }

        return $this->routeController->{$method}();
    }
}
