<?php
namespace YourProject\Application;

use Fewlines\Http\Router;
use Fewlines\Http\Header;
use Fewlines\Locale\Locale;

class Bootstrap extends \Fewlines\Application\Bootstrap
{
	public function initLocale() {
		$locale = Router::getInstance()->getRouteUrlPart('locale');

		if ($locale != "de"){
			// Header::set(404);
		}

		Locale::set($locale);
	}
}