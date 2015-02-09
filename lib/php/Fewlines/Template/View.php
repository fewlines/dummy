<?php

namespace Fewlines\Template;

use Fewlines\Helper\PathHelper;
use Fewlines\Http\Header as HttpHeader;
use Fewlines\Http\Request as HttpRequest;

class View
{
	/**
	 * The layout which the view uses
	 *
	 * @var \Fewlines\Template\Layout
	 */
	private $layout;

	/**
	 * The name of the view (could be overwritten
	 * by anything e.g. a 404 error)
	 *
	 * @var string
	 */
	private $viewName;

	/**
	 * The real viewname as in the url
	 *
	 * @var string
	 */
	private $realViewName;

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
	private $viewPath;

	/**
	 * Controller class of the current view
	 *
	 * @var string
	 */
	private $controllerClass;

	/**
	 * Controller of the current view
	 *
	 * @var \Fewlines\Controller\Template
	 */
	public $controller;

	/**
	 * Returns the name of the rendered view
	 *
	 * @return string
	 */
	public function getViewName()
	{
		return $this->viewName;
	}

	/**
	 * Gets the name of the first view
	 * (no overwrites)
	 *
	 * @return string
	 */
	public function getRealViewName()
	{
		return $this->realViewName;
	}

	/**
	 * Returns the path to the current view
	 *
	 * @return string
	 */
	public function getViewPath()
	{
		return $this->viewPath;
	}

	/**
	 * Returns the view action
	 *
	 * @return string
	 */
	public function getViewAction()
	{
		return $this->action;
	}

	/**
	 * Sets the view path
	 *
	 * @param string $view
	 */
	public function setViewPath($view)
	{
		$layout   = $this->layout->getLayoutName();
		$viewFile = PathHelper::getRealViewPath($view, $layout);

		if(false == file_exists($viewFile))
		{
			$viewFile = $this->set404Eror();
		}

		$this->viewPath = $viewFile;
	}

	/**
	 * Sets the view action
	 *
	 * @param string $action
	 */
	public function setViewAction($action)
	{
		$this->action = $action;
	}

	/**
	 * Init the view with some options
	 * called from the layout
	 *
	 * @param string                    $viewName
	 * @param string                    $action
	 * @param \Fewlines\Template\Layout $layout
	 */
	public function setView($viewName, $action, \Fewlines\Template\Layout $layout)
	{
		$this->layout = $layout;

		// Set view components
		$this->setViewAction($action);
		$this->setViewName($viewName);
		$this->setViewPath($viewName);
		$this->setController("Fewlines\Controller\View\\");
	}

	/**
	 * Sets the default 404 error
	 * file
	 *
	 * @return string
	 */
	public function set404Eror()
	{
		HttpHeader::setHeader404();
		$viewName = defined('DEFAULT_ERROR_VIEW')
					? DEFAULT_ERROR_VIEW
					: 'error';

		// Set the action to index (prevent unexpected actions)
		$defaultAction = HttpRequest::getInstance()->getDefaultDestination('action');
		$this->setViewAction($defaultAction);
		$this->setViewName($viewName);

		return PathHelper::getRealViewPath($viewName);
	}

	/**
	 * Sets the name of the view
	 *
	 * @param string $name
	 */
	private function setViewName($name)
	{
		if(is_null($this->realViewName))
		{
			$this->realViewName = $name;
		}

		$this->viewName = $name;
	}

	/**
	 * Sets the controller namespace
	 *
	 * @param string $path
	 */
	private function setController($path)
	{
		$this->controllerClass = $path . $this->viewName;
	}

	/**
	 * Returns the instantiated controller
	 * if exists
	 *
	 * @return *
	 */
	public function getController()
	{
		return $this->controller;
	}

	/**
	 * Init the controller of the current view
	 * (if exists)
	 *
	 * @return boolean
	 */
	public function initViewController()
	{
		if(class_exists($this->controllerClass))
		{
			$this->controller = new $this->controllerClass;
			$this->controller->init($this->template);

			$this->callViewAction($this->action . "Action");

			return true;
		}

		return false;
	}

	/**
	 * Calls the action of the current controller
	 *
	 * @param string $method
	 */
	private function callViewAction($method)
	{
		if(!method_exists($this->controller, $method))
		{
			throw new Exception\ActionNotFoundException(
				"Could not found the action (method)
				\"" . $method . "\". Check the controller
				for it!"
			);
		}

		$this->controller->{$method}();
	}
}