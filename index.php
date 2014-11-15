<?php
	// Constants
	define("ROOT_DIR", __DIR__);
	define("FEWLINES_PHP", ROOT_DIR . '/php');

	define("URL_LAYOUT_ROUTE", "/view:index/action:index");

	define("AL_FNC", '\Fewlines\Autoloader\Autoloader::loadClass');

	// Set include path for the php libs
	set_include_path(implode(PATH_SEPARATOR, array(
			FEWLINES_PHP, get_include_path()
		)));

	// Inlcude the application ("bootstrap")
	require_once "Fewlines/Application/Application.php";
echo "<pre>";
	// Instantiate the application
	$application = new \Fewlines\Application\Application(AL_FNC);
	$application->run();
echo "</pre>";
?>