<?php

/**
 * fewlines CMS
 *
 * --------------------------------------------------
 *
 * The init file holds all important config files
 * for the framework of fewlines. Please do NOT change
 * anything in here.
 *
 * --------------------------------------------------
 *
 * All paths required by the application
 *
 * --------------------------------------------------
 *
 * Do not change this constants if you aren't
 * a developer of fewlines. It can crash the
 * whole system if a constant was changed.
 */

define("ROOT_DIR",     __DIR__);
define("ETC_PATH",     ROOT_DIR . "/etc");
define("LIB_PATH",     ROOT_DIR . "/lib");
define("TPL_PATH",     ETC_PATH . "/template");
define("FEWLINES_PHP", LIB_PATH . "/php");
define("LOCALE_PATH",  ETC_PATH . "/locale");
define("LAYOUT_PATH",  TPL_PATH . "/fewlines/layout");
define("VIEW_PATH",    TPL_PATH . "/fewlines/views");

/**
 * Define the application environment
 */

defined("APPLICATION_ENV") || (
	define("APPLICATION_ENV", getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production')
);

/**
 * Default options for the
 * views, layout, ...
 */

define("LAYOUT_FILETYPE",    "phtml");
define("VIEW_FILETYPE",      "phtml");
define("DEFAULT_ERROR_VIEW", "error");
define("DEFAULT_LAYOUT",     "default");
define("INSTALL_LAYOUT",     "install");
define("EXCEPTION_LAYOUT",   "exception");
define("AUTLOADER_LC",       "\Fewlines\Autoloader\Autoloader::loadClass");
define("URL_LAYOUT_ROUTE",   "/view:index/action:index");
define("DEVELOPER_DEBUG",    true);

/**
 * Set include paths for the autoloader
 * component
 */

set_include_path(implode(PATH_SEPARATOR, array(
		FEWLINES_PHP, get_include_path()
	)));

/**
 * A collection a all config folders
 * and the filetype to define the config
 * type
 *
 * ------------------------------------
 *
 * Config example:
 *
 * array(
 *     "dir"  => PATH_TO_FOLDER
 *     "type" => "xml/php/json/..."
 * )
 */

function getConfig()
{
	return array(
		array(
			"dir"  => ETC_PATH . "/config/fewlines",
			"type" => "xml"
		)
	);
}

/**
 * Register the autoloader
 */

require_once "Fewlines/Autoloader/Autoloader.php";
spl_autoload_register(AUTLOADER_LC);

/**
 * Global debug function (beautified output)
 */
function pr($input)
{
	echo "<pre>";
	if(is_bool($input)){var_dump($input);}
	else{print_r($input);}
	echo "</pre>";
}