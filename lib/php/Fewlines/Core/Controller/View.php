<?php
namespace Fewlines\Core\Controller;

use Fewlines\Core\Helper\UrlHelper;
use Fewlines\Core\Application\Config;
use Fewlines\Core\Http\Request as HttpRequest;
use Fewlines\Core\Http\Router;

class View implements IView
{
	/**
	 * Holds the whole
	 * template instance
	 *
	 * @var \Fewlines\Core\Template\Template
	 */
	protected $template;

	/**
	 * @var \Fewlines\Core\Http\Request
	 */
	protected $httpRequest;

	/**
	 * @var \Fewlines\Core\Http\Response
	 */
	protected $httpResponse;

	/**
	 * @var array
	 */
	private $usedAssignNames = array();

	/**
	 * Assigns a var to
	 * the active template
	 *
	 * @param  string $name
	 * @param  *	  $content
	 * @return *
	 */
	protected function assign($name, $content) {
		if (true == property_exists($this->template, $name)) {
			if(false == in_array($name, $this->usedAssignNames)) {
				throw new Exception\PropertyExistException("Could not assign the variable
					\"" . $name . "\". The property
					already exists.");
			}
		}

		$this->template->$name = $content;
		$this->usedAssignNames[] = $name;

		return $content;
	}

	/**
	 * Inits with the template
	 *
	 * @param  \Fewlines\Core\Template\Template $template
	 */
	public function init(\Fewlines\Core\Template\Template &$template) {
		$this->template = $template;
		$this->httpRequest = Router::getInstance()->getRequest();
		$this->httpResponse = $this->httpRequest->getResponse();
	}

	/**
	 * Get the instantiated config instance
	 *
	 * @return \Fewlines\Core\Application\Config
	 */
	protected function getConfig() {
		return Config::getInstance();
	}

	/**
	 * Redirects
	 *
	 * @param string $url
	 */
	protected function redirect($url) {
		HttpHeader::redirect($url);
	}

	/**
	 * Returns the base url
	 *
	 * @param  string|array $parts
	 * @return string
	 */
	protected function getBaseUrl($parts = "") {
		return UrlHelper::getBaseUrl($parts);
	}

	/**
	 * @param  string $view
	 * @return string
	 */
	protected function render($view) {
		return $this->template->renderView($view);
	}
}
