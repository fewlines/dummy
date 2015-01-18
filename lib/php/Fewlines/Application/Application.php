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

use Fewlines\Handler\Error as ErrorHandler;
use Fewlines\Http\Request as HttpRequest;
use Fewlines\Http\Header as HttpHeader;
use Fewlines\Handler\Exception as ExceptionHandler;
use Fewlines\Helper\UrlHelper;
use Fewlines\Template\Template;
use Fewlines\Session\Session;

class Application
{
	/**
	 * @var string
	 */
	const INSTALL_VIEW = "install";

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
	 * Holds the config object (config files
	 * defined by the user)
	 *
	 * @var \Fewlines\Application\Config
	 */
	private $config;

	/**
	 * Inits the application components
	 */
	public function __construct()
	{
		// Register sessions
		Session::startSession();
		Session::initCookies();

		// Register required components
		$this->registerErrorHandler();
		$this->registerHttpRequest();
		$this->registerTemplate();
	}

	/**
	 * Set the dirs which contains the config
	 * files
	 *
	 * @param  array $configDirs
	 * @return \Fewlines\Application\Application
	 */
	public function setConfig($configDirs)
	{
		$this->config = new Config($configDirs);
		return $this;
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

		// Check if application is installed already
		if(false == $this->isInstalled())
		{
			$viewName = $this->template->getLayout()->getRealViewName();

			if($viewName != self::INSTALL_VIEW)
			{
				$this->installApplication();
			}
			else
			{
				$this->template->setLayout(self::INSTALL_VIEW);
			}
		}

		try
		{
			// Start buffer for application
			ob_start();

			// Render the frontend
			$this->renderApplication();
		}
		catch(\Exception $err)
		{
			// Clear just rendered content
			ob_end_flush();
			ob_clean();

			// Change layout to exception
			$this->template->setLayout(EXCEPTION_LAYOUT);
			$this->renderApplication();
		}
	}

	/**
	 * Check if the application was already
	 * installed
	 *
	 * @return boolean
	 */
	private function isInstalled()
	{
		return (bool) Config::getInstance()->getElementByPath('installed');
	}

	/**
	 * Leads the user to the installation
	 */
	private function installApplication()
	{
		// Redirect to the install view
		$url = array(self::INSTALL_VIEW, "step1");
		HttpHeader::redirect(UrlHelper::getBaseUrl($url));
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
	 * Set the error handling function
	 * to transform erros to execptions
	 */
	private function registerErrorHandler()
	{
		/**set_error_handler(
			array(new ErrorHandler(), 'handleError')
		);*/
	}
}