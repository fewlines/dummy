<?php
	// Constants
	define("ROOT_DIR", __DIR__);
	define("FEWLINES_PHP", ROOT_DIR . '/php');
	define("AL_FNC", '\Fewlines\Autoloader::loadClass');

	// Set include path for the php libs
	set_include_path(implode(PATH_SEPARATOR, array(
			FEWLINES_PHP, get_include_path()
		)));

	// Include the Autoloader
	require_once "Fewlines/Autoloader.php";

	// Inlcude the application ("bootstrap")
	require_once "Fewlines/Application.php";

	// Instantiate the application
	$application = new \Fewlines\Application(AL_FNC);
	$application->run();
?>