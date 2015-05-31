<?php
namespace YourProject\Application;

use Fewlines\Core\Http\Router;
use Fewlines\Core\Http\Header;
use Fewlines\Core\Locale\Locale;

class Bootstrap extends \Fewlines\Core\Application\Bootstrap
{
	public function initLocale() {
		$locale = Router::getInstance()->getRouteUrlPart('locale');

		if ($locale != "de"){
			// Header::set(404);
		}

		Locale::set($locale);
	}
}