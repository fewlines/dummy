<?php
namespace YourProject\Application;

use Fewlines\Application\Application;
use Fewlines\Http\Router;
use Fewlines\Locale\Locale;
use Fewlines\Csv\Csv;
use Fewlines\Http\Header;

class Bootstrap
{
	/**
	 * @param \Fewlines\Application\Application $application Running application
	 */
	public function __construct($application) {
		// Application::getEnv()->set('production');
		Locale::set(Router::getInstance()->getRouteUrlPart('locale'));
	}
}