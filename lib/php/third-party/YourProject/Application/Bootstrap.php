<?php
namespace YourProject\Application;

use Fewlines\Http\Router;
use Fewlines\Locale\Locale;
use Fewlines\Csv\Csv;

class Bootstrap {
	private $app;

	/**
	 * @param \Fewlines\Application\Application $app
	 */
	public function __construct($app) {
		$this->app = $app;

		$router = Router::getInstance();
		$this->setLocale($router->getRouteUrlPart('locale'));
	}

	private function setLocale($locale) {
		Locale::set($locale);
	}
}