<?php
namespace Fewlines\Template;

use Fewlines\Template\Template;
use Fewlines\Helper\PathHelper;
use Fewlines\Helper\NamespaceConfigHelper;
use Fewlines\Http\Header as HttpHeader;
use Fewlines\Http\Request as HttpRequest;

class View
{

    /**
     * @var string
     */
    const ACTION_SUFFIX = 'Action';

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
     * @var \Fewlines\Http\Router\Routes\Route
     */
    private $activeRoute;

    /**
     * Controller instance of the current view
     *
     * @var \Fewlines\Controller\View
     */
    public $viewController;

    /**
     * Controller of the route
     *
     * @var \Fewlines\Controller\View
     */
    private $routeController;

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

        if (false == file_exists($viewFile)) {
            $viewFile = $this->set404Eror();
        }

        $this->path = $viewFile;
    }

    /**
     * Init the view with some options
     * called from the layout
     *
     * @param array $urlParts
     */
    public function __construct($urlParts) {
        // Set components by default layout
        if(true == array_key_exists('view', $urlParts) &&
            true == array_key_exists('action', $urlParts)) {
            $this->setAction($urlParts['action']);
            $this->setName($urlParts['view']);
            $this->setPath($urlParts['view']);
            $this->setViewControllerClass(NamespaceConfigHelper::getNamespaces('php'));
        }

        // Set by route
        if(true == ($urlParts instanceof \Fewlines\Http\Router\Routes\Route)) {
            $this->activeRoute = $urlParts;
            $this->setRouteControllerClass($this->activeRoute->getToClass());
        }
    }

    /**
     * Sets the default 404 error
     * file
     *
     * @return string
     */
    public function set404Eror() {
        HttpHeader::setHeader404();
        $name = defined('DEFAULT_ERROR_VIEW') ? DEFAULT_ERROR_VIEW : 'error';

        // Set the action to index (prevent unexpected actions)
        $defaultAction = HttpRequest::getInstance()->getDefaultDestination('action');
        $this->setAction($defaultAction);
        $this->setName($name);

        return PathHelper::getRealViewPath($name);
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
     * Sets the controller namespace
     *
     * @param array $path
     */
    private function setViewControllerClass($paths) {
        $controllerPath = "\\Controller\\View\\";
        $namespace = "\\Fewlines" . $controllerPath;

        foreach ($paths as $path) {
            $path = "\\" . $path . $controllerPath;

            if (true == class_exists($path . $this->name)) {
                $namespace = $path;
            }
        }

        $this->controllerClass = $namespace . $this->name;

        if(false == class_exists($this->controllerClass)) {
            throw new View\Exception\ControllerClassNotFoundException(
                'The class "' . $this->controllerClass . '" for the
                controller was not found.'
            );
        }
    }

    /**
     * Set the controller
     * for the route
     *
     * @param string $class
     */
    public function setRouteControllerClass($class) {
        $method = strtolower(HttpRequest::getInstance()->getHttpMethod());
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
            throw new View\Exception\InvalidHttpMethodException(
                    'Invalid HTTP method found'
                );
        }
    }

    /**
     * Returns the instantiated view
     * controller if exists
     *
     * @return \Fewlines\Controller\View
     */
    public function getViewController() {
        return $this->viewController;
    }

    /**
     * Returns the instantiated route
     * controller if exists
     *
     * @return \Fewlines\Controller\View
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
        if(false == is_null($this->activeRoute)) {
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

        if(true == ($this->viewController instanceof \Fewlines\Controller\View)) {
            $this->viewController->init(Template::getInstance());
            return $this->callViewAction($this->getAction() . self::ACTION_SUFFIX);
        }
        else {
            throw new View\Exception\ControllerInitialisationGoneWrongException(
                'The view controller could not be initialised.
                Must be instance of \Fewlines\Controller\View'
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

        if(true == ($this->routeController instanceof \Fewlines\Controller\View)) {
            $this->routeController->init(Template::getInstance());
            return $this->callRouteMethod($this->activeRoute->getToMethod());
        }
        else {
            throw new View\Exception\ControllerInitialisationGoneWrongException(
                'The route controller could not be initialised.
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
            throw new View\Exception\ActionNotFoundException(
                'Could not found the action (method) "' . $method . '"
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
            throw new View\Exception\MethodNotFoundException(
                'Could not found the method "' . $method . '"
                - Check the controller "' . $this->controllerClass . '"
                for it');
        }

        return $this->routeController->{$method}();
    }
}
