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
define("FEWLINES_PHP", ROOT_DIR . "/php");
define("LAYOUT_PATH",  ROOT_DIR . "/templates/fewlines/layout");
define("VIEW_PATH",    ROOT_DIR . "/templates/fewlines/views");

/**
 * Default options for the 
 * views, layout, ...
 */

define("LAYOUT_FILETYPE",    "phtml");
define("VIEW_FILETYPE",      "phtml");
define("DEFAULT_ERROR_VIEW", "error");
define("DEFAULT_LAYOUT",     "default");
define("INSTALL_LAYOUT",     "install");
define("AUTLOADER_LC",       "\Fewlines\Autoloader\Autoloader::loadClass");
define("URL_LAYOUT_ROUTE",   "/view:index/action:index");

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
			"dir"  => ROOT_DIR . "/config/fewlines",
			"type" => "xml"
		)
	);
}

/**
 * Register the autoloader
 */

require_once "Fewlines/Autoloader/Autoloader.php";
spl_autoload_register(AUTLOADER_LC);