<?php
namespace Fewlines\Http;

use Fewlines\Application\Config;
use Fewlines\Helper\ArrayHelper;

class Router extends Router\Routes
{
	/**
	 * Holds the base url
	 *
	 * @var string
	 */
	private $baseUrl;

	/**
	 * @var string
	 */
	private $url;

	/**
	 * The active route will be set
	 * if the url matches a given route
	 *
	 * @var \Fewlines\Http\Router\Routes\Route
	 */
	private $activeRoute;

	/**
	 * The layout the user defined with
	 * the config
	 *
	 * @var string
	 */
	private $routeLayout;

	/**
	 * Holds the given request
	 *
	 * @var \Fewlines\Http\Request
	 */
	private $request;

	/**
	 * @var \Fewlines\Http\Router
	 */
	private static $instance;

	/**
	 * Init the router with a
	 * given Request
	 *
	 * @param \Fewlines\Http\Request $request
	 */
	public function __construct(Request $request) {
		// Add routes
		$routes = Config::getInstance()->getElementByPath('route');
		if ($routes != false) {
			foreach ($routes->getChildren() as $route) {
				$name = strtolower($route->getName());

				// Add route to parent
				if (true == preg_match(HTTP_METHODS_PATTERN, $name)) {
					$this->addRoute($name, $route->getAttribute('from'), $route->getAttribute('to'));
				}
			}
		}

		// Add layout if exists
		$layout = $routes->getChildByName('layout');
		if ($layout != false) {
			$this->routeLayout = $layout->getContent();
		}

		// Set url components
		$this->setBaseUrl();
		$this->url = $request->getUrl();

		// Check if route is active
		$currentUrl = implode('/', $this->getUrlParts());
		foreach ($this->routes as $route) {
			if (ltrim($route->getFrom(), '/') == $currentUrl) {
				$this->activeRoute = $route;
			}
		}

		// Save request
		$this->request = $request;

		// Save instance for further usage
		self::$instance = $this;
	}

	/**
	 * @return \Fewlines\Http\Router
	 */
	public static function getInstance() {
		return self::$instance;
	}

	/**
	 * @return \Fewlines\Http\Request
	 */
	public function getRequest() {
		return $this->request;
	}

	/**
	 * Sets the baseurl
	 */
	private function setBaseUrl() {
		$this->baseUrl = preg_replace('/index\.php/', '', $_SERVER['PHP_SELF']);
	}

	/**
	 * Returns the base url
	 *
	 * @return string
	 */
	public function getBaseUrl() {
		return $this->baseUrl;
	}

	/**
	 * Returns the action route
	 * url layout
	 *
	 * @return string
	 */
	protected function getUrlLayout() {
		return false == is_null($this->routeLayout) ? $this->routeLayout : URL_LAYOUT_ROUTE;
	}

	/**
	 * Returns a list of all get paramters
	 * given
	 *
	 * @return array
	 */
	protected function getUrlPartsGET() {
		return $_GET;
	}

	/**
	 * Returns a list of all post paramters
	 * given
	 *
	 * @return array
	 */
	protected function getUrlPartsPOST() {
		return $_POST;
	}

	/**
	 * Gets all the methods from the url layout
	 * without the default option
	 *
	 * @return array
	 */
	private function getUrlLayoutMethods() {
		$urlLayoutParts = explode("/", $this->getUrlLayout());
		$urlLayoutParts = ArrayHelper::clean($urlLayoutParts);

		for ($i = 0; $i < count($urlLayoutParts); $i++) {
			$urlLayoutParts[$i] = reset(explode(":", $urlLayoutParts[$i]));
		}

		return $urlLayoutParts;
	}

	/**
	 * Retuns the pattern created by the url layout
	 * route
	 *
	 * @return string
	 */
	private function getUrlLayoutPattern() {
		return '/' . implode('|', $this->getUrlLayoutMethods()) . '/';
	}

	/**
	 * Returns a part from the url
	 * defined by the key
	 *
	 * @param  string $key
	 * @return string
	 */
	public function getRouteUrlPart($key) {
		$parts = $this->getRouteUrlParts();
		$value = '';

		if (true == ($parts instanceof \Fewlines\Http\Router\Routes\Route)) {
			$value = '';
		}
		else {
			if (true == array_key_exists($key, $parts)) {
				$value = $parts[$key];
			}
		}

		return $value;
	}

	/**
	 * Returns the url parts relative to
	 * the layout or route
	 *
	 * @return array|\Fewlines\Http\Router\Routes\Route
	 */
	public function getRouteUrlParts() {
		/**
		 * User defined route
		 */

		if (true ==($this->activeRoute instanceof \Fewlines\Http\Router\Routes\Route)) {
			return $this->activeRoute;
		}

		/**
		 * Standard view, action route handling
		 */

		$layoutRoute = $this->getUrlLayout();
		$urlParts = $this->getUrlParts();
		$routeUrlContent = array();

		// Set default content with destination
		$urlLayoutParts = $this->getUrlLayoutMethods();

		for ($i = 0; $i < count($urlLayoutParts); $i++) {
			$method = $urlLayoutParts[$i];
			$routeUrlContent[$method] = $this->getDefaultDestination($method);
		}

		// Get the position of a method (view or action)
		preg_match_all($this->getUrlLayoutPattern(), $layoutRoute, $matches);
		$routeOrder = $matches[0];

		// Parse the url witht the route order
		if (false == empty($urlParts)) {
			// Get parameters for the application
			for ($i = 0; $i < count($routeOrder); $i++) {
				if (array_key_exists($i, $urlParts)) {
					if($urlParts[$i] != '') {
						$routeUrlContent[$routeOrder[$i]] = $urlParts[$i];
					}
				}
			}

			// Get the other parameters
			if (count($urlParts) > count($routeOrder)) {
				$routeUrlContent['parameters'] = array();

				for ($i = count($routeOrder); $i < count($urlParts); $i+= 2) {
					$key = $urlParts[$i];
					$content = array_key_exists($i + 1, $urlParts) ? $urlParts[$i + 1] : '';

					$routeUrlContent['parameters'][$key] = $content;
				}

				// Normal get parameters
				$getParams = $this->getUrlPartsGET();

				// Parameters set by user
				$userParams = $routeUrlContent['parameters'];

				// Set user parameters as default get paramters
				foreach ($userParams as $name => $value) {
					$_GET[$name] = $value;
				}

				// Append normal get parts (if set)
				if (false == empty($getParams)) {
					foreach ($getParams as $name => $value) {
						$routeUrlContent['parameters'][$name] = $value;
					}
				}
			}
		}

		return $routeUrlContent;
	}

	/**
	 * Returns the default destination for
	 * a method (e.g. view:index)
	 *
	 * @param  string $method
	 * @return string
	 */
	public function getDefaultDestination($method) {
		$urlLayout = explode('/', $this->getUrlLayout());
		$urlLayout = ArrayHelper::clean($urlLayout);
		$defaultMethod = 'index';

		for ($i = 0; $i < count($urlLayout); $i++) {
			if (true == preg_match('/' . $method . ':/', $urlLayout[$i])) {
				$defaultMethod = end(explode(':', $urlLayout[$i]));
			}
		}

		return $defaultMethod;
	}

	/**
	 * Returns all parameters
	 * from the url
	 *
	 * @return array
	 */
	private function getUrlParts() {
		$baseUrlPattern = ltrim($this->getBaseUrl(), "/");
		$baseUrlPattern = '/' . preg_replace('/\//', '\/', $baseUrlPattern) . '/';

		$url = preg_replace($baseUrlPattern, '', $this->url);
		$parts = explode('/', $url);
		array_shift($parts);

		$realParts = array();

		for ($i = 0; $i < count($parts); $i++) {

			if (false == empty($parts[$i])) {

				/**
				 * Check if get parameters are
				 * given in this part
				 */
				if (true == preg_match('/\?(.*)/', $parts[$i])) {
					$parts[$i] = reset(explode('?', $parts[$i]));
				}

				$realParts[] = $parts[$i];
			}
			else {
				$realParts[] = '';
			}
		}

		return $realParts;
	}
}
