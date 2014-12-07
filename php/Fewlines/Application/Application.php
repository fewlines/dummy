<?php
/**
 * fewlines CMS
 *
 * Description: This class calls all the necessary functions
 * to build the rendered view
 *
 * @copyright Copyright (c) fewlines
 * @author Davide Perozzi
 */

namespace Fewlines\Application;

require_once "Fewlines/Autoloader/Autoloader.php";

use Fewlines\Handler\Error as ErrorHandler;
use Fewlines\Http\Request as HttpRequest;
use Fewlines\Template\Template as Template;
use Fewlines\Database\Database as Database;
use Fewlines\Session\Session;

class Application
{
	/**
	 * Tells wether the application was already
	 * runned or not
	 *
	 * @var boolean
	 */
	private $isRunning = false;

	/**
	 * Instance of the request object
	 *
	 * @var \Fewlines\Http\Request
	 */
	private $httpRequest;

	/**
	 * Holds the current template which was
	 * build together
	 *
	 * @var \Fewlines\Template\Template
	 */
	private $template;

	/**
	 * Inits the application components
	 */
	public function __construct()
	{
		// Add autoloader
		$autoloader = '\Fewlines\Autoloader\Autoloader::loadClass';
		$this->registerAutoloader($autoloader);

		// Register session
		Session::startSession();
		Session::initCookies();

		// Register all components
		$this->registerErrorHandler();
		$this->registerHttpRequest();
		$this->registerTemplate();
	}

	/**
	 * Gets all http request informations
	 */
	public function registerHttpRequest()
	{
		$this->httpRequest = new HttpRequest();
	}

	/**
	 * Get the template with the
	 * http request
	 */
	private function registerTemplate()
	{
		$this->template = new Template(
				$this->httpRequest->getUrlMethodContents()
			);
	}

	/**
	 * Renders the applications frontend
	 */
	private function renderApplication()
	{
		$this->template->render();
	}

	/**
	 * Runs the application
	 *
	 * @return boolean
	 */
	public function run()
	{
		$this->isRunning = true;

		// Render the frontend
		$this->renderApplication();
	}

	/**
	 * Returns the state of the application
	 *
	 * @return boolean
	 */
	public function isRunning()
	{
		return $this->isRunning;
	}

	/**
	 * Registers the autoload function
	 *
	 * @param  string
	 * @return booleam
	 */
	private function registerAutoloader($fnc)
	{
		return spl_autoload_register($fnc);
	}

	/**
	 * Set the error handling function
	 * to transform erros to execptions
	 */
	private function registerErrorHandler()
	{
		set_error_handler(
			array(new ErrorHandler(), 'handleError')
		);
	}
}

?>