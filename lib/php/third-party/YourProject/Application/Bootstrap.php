<?php
namespace YourProject\Application;

use Fewlines\Http\Router;
use Fewlines\Locale\Locale;

class Bootstrap extends \Fewlines\Application\Bootstrap
{
	public function initLocale() {
		echo "init locale";
		Locale::set(Router::getInstance()->getRouteUrlPart('locale'));
	}
}