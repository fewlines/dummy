<?php
namespace YourProject\Application;

use Fewlines\Application\Application;
use Fewlines\Http\Router;
use Fewlines\Locale\Locale;
use Fewlines\Csv\Csv;
use Fewlines\Http\Header;

class Bootstrap extends \Fewlines\Application\Bootstrap
{
	public function initLocale() {
		Locale::set(Router::getInstance()->getRouteUrlPart('locale'));
	}
}