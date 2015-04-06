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
define("LIB_PHP",      LIB_PATH . "/php");
define("LIB_PHP_TP",   LIB_PHP  . "/third-party");
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
 * views, layout, routes, ...
 */

define('LAYOUT_FILETYPE',      'phtml');
define('VIEW_FILETYPE',        'phtml');
define('DEFAULT_ERROR_VIEW',   'error');
define('DEFAULT_LAYOUT',       'default');
define('INSTALL_LAYOUT',       'install');
define('EXCEPTION_LAYOUT',     'exception');
define('AUTLOADER_LC',         '\Fewlines\Autoloader\Autoloader::loadClass');
define('HTTP_METHODS_PATTERN', '/get|post|put|delete|any/');
define('URL_LAYOUT_ROUTE',     '/view:index/action:index');
define('FNC_REGEX_PARSER',     '/\{\{([^\}]*)\}\}/');
define('DEVELOPER_DEBUG',      true);
define('DR_SP',                '/');
define('DEFAULT_LOCALE',       'de');

/**
 * Set include paths for the autoloader
 * component
 */

set_include_path(implode(PATH_SEPARATOR, array(
		LIB_PHP, LIB_PHP_TP, get_include_path()
	)));

/**
 * A collection a all config folders
 * and the filetype to define the config
 * type. The given folder will scan all files
 * by the type (recursive). Same xml trees
 * won't be merged.
 *
 * ------------------------------------
 *
 * Config example:
 *
 * array(
 *     "dir"  => PATH_TO_FOLDER
 *     "type" => "xml"
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