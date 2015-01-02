<?php

	/**
	 * fewlies Framework (CMS)
	 *
	 *  - Copyright:  fewlines
	 *  - Developers: Davide Perozzi
	 *
	 *  - inspired by Zend Framework (http://framework.zend.com/)
	 *
	 * -------------------------------------
	 *
	 * Note: We are searching for a german developer!
	 * Feel free to contact us.
	 *
	 * Skills: PHP, JavaScript (Google closure), HTML, CSS, ...
	 * ... and all skills a web developer needs.
	 *
	 * -------------------------------------
	 *
	 * Do not change this constants if you aren't
	 * a developer of fewlines. It can crash the
	 * whole system if a constant was changed.
	 *
	 * If you find any bugs feel free to contact
	 * a developer. Or became a developer and help
	 * to improve fewlines ;)
	 */

	define("ROOT_DIR",     __DIR__);
	define("FEWLINES_PHP", ROOT_DIR . "/php");
	define("LAYOUT_PATH",  ROOT_DIR . "/templates/fewlines/layout");
	define("VIEW_PATH",    ROOT_DIR . "/templates/fewlines/views");

	define("LAYOUT_FILETYPE", "phtml");
	define("VIEW_FILETYPE",   "phtml");

	define("DEFAULT_ERROR_VIEW", "error");

	define("DEFAULT_LAYOUT", "default");
	define("INSTALL_LAYOUT", "install");

	/**
	 * To define a other route for the
	 * framework uncomment the constant
	 * below and set the parts you need
	 *
	 * -----------------------------------
	 *
	 * define("URL_LAYOUT_ROUTE", "/view:index/action:index");
	 */

	// Set include path for the php libs
	set_include_path(implode(PATH_SEPARATOR, array(
			FEWLINES_PHP, get_include_path()
		)));

	// Debug function
	function pr($optIn)
	{
		echo "<pre>";
			if(is_bool($optIn))
			{
				var_dump($optIn);
			}
			else
			{
				print_r($optIn);
			}
		echo "</pre>";
	}

	/**
	 * Include the application and run it
	 * with specific config files
	 *
	 * -------------------------------------
	 *
	 * Do not add config dirs which can overwrite
	 * core xml config files.
	 */

	// Inlcude the application
	require_once "Fewlines/Application/Application.php";

	// Define config dirs
	$configs = array(
		array(
			"dir"  => ROOT_DIR . "/config/fewlines",
			"type" => "xml"
		)
	);

	/**
	 * Instantiate the application
	 * It will be installed by itself
	 *
	 * -----------------------------------------
	 *
	 * To reactivate the installation,
	 * please go to "/config/fewlines/"
	 * and uncomment the file "Install.xml".
	 * Just rename it to "_Install.xml", so the
	 * application will ignore it and pretend
	 * to be a uninstalled version of fewlines
	 */

	$application = new \Fewlines\Application\Application;
	$application->setConfig($configs);
	$application->run();

?>