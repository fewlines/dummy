<?php
	// Constants
	define("ROOT_DIR", __DIR__);
	define("FEWLINES_PHP", ROOT_DIR . '/php');
	define("LAYOUT_PATH", ROOT_DIR . '/templates/fewlines/layout');
	define("VIEW_PATH", ROOT_DIR . '/templates/fewlines/views');

	define("LAYOUT_FILETYPE", 'phtml');
	define("VIEW_FILETYPE", "phtml");

	define("DEFAULT_ERROR_VIEW", "error");
	define("DEFAULT_LAYOUT", 'layout');

	//define("URL_LAYOUT_ROUTE", "/view:index/action:index");

	// Set include path for the php libs
	set_include_path(implode(PATH_SEPARATOR, array(
			FEWLINES_PHP, get_include_path()
		)));

	// Inlcude the application ("bootstrap")
	require_once "Fewlines/Application/Application.php";

	// Define config dirs
	$configs = array(
		array(
			"dir"  => ROOT_DIR . "/config/fewlines",
			"type" => 'xml'
		)
	);

	function pr($optIn)
	{
		echo "<pre>";
		print_r($optIn);
		echo "</pre>";
	}

	// Instantiate the application
	$application = new \Fewlines\Application\Application;
	$application->setConfig($configs);
	$application->run();
?>