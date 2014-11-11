<?php
	define("ROOT_DIR", __DIR__);
	define("FEWLINES_PHP", ROOT_DIR . '/php');

	// Set include path for the php libs
	set_include_path(implode(PATH_SEPARATOR, array(
			FEWLINES_PHP, get_include_path()
		)));

	// Use autoload method from php
	function __autoload($classPath)
	{
    	require_once $classPath . ".php";
	}

	// Inlcude the application ("bootstrap")
	require_once "Fewlines/Application.php";

	// Instantiate the application
	$application = new \Fewlines\Application;
	$application->run();
?>