<?php
namespace Fewlines\Template;

use Fewlines\Helper\PathHelper;
use Fewlines\Http\Header as HttpHeader;
use Fewlines\Http\Request as HttpRequest;
use Fewlines\Template\Template;
use Fewlines\Helper\NamespaceConfigHelper;

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
     * Controller instance of the current view
     *
     * @var \Fewlines\Controller\Template
     */
    public $controller;

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
     * @param string $name
     * @param string $action
     */
    public function __construct($name, $action) {
        // Set view components
        $this->setAction($action);
        $this->setName($name);
        $this->setPath($name);
        $this->setControllerClass(NamespaceConfigHelper::getNamespaces('php'));
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
        if (is_null($this->realName)) {
            $this->realName = $name;
        }

        $this->name = $name;
    }

    /**
     * Sets the controller namespace
     *
     * @param array $path
     */
    private function setControllerClass($paths) {
        $controllerPath = "\\Controller\\View\\";
        $namespace = "\\Fewlines" . $controllerPath;

        foreach($paths as $path) {
            $path = "\\" . $path . $controllerPath;

            if(true == class_exists($path . $this->name)) {
                $namespace = $path;
            }
        }

        $this->controllerClass = $namespace . $this->name;
    }

    /**
     * Returns the instantiated controller
     * if exists
     *
     * @return *
     */
    public function getController() {
        return $this->controller;
    }

    /**
     * Init the controller of the current view
     * (if exists)
     *
     * @return null|*
     */
    public function initViewController() {
        if (class_exists($this->controllerClass)) {
            $this->controller = new $this->controllerClass;
            $this->controller->init(Template::getInstance());

            return $this->callViewAction($this->getAction() . self::ACTION_SUFFIX);
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
        if (false == method_exists($this->controller, $method)) {
            throw new Exception\ActionNotFoundException("Could not found the action (method)
				\"" . $method . "\". Check the controller
				for it!");
        }

        return $this->controller->{$method}();
    }
}
