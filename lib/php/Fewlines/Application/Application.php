<?php
namespace Fewlines\Application;

use Fewlines\Handler\Error as ErrorHandler;
use Fewlines\Http\Request as HttpRequest;
use Fewlines\Http\Header as HttpHeader;
use Fewlines\Handler\Exception as ExceptionHandler;
use Fewlines\Helper\UrlHelper;
use Fewlines\Template\Template;
use Fewlines\Session\Session;
use Fewlines\Locale\Locale;

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
    public function __construct() {
        // Set locale
        Locale::set('de');

        // Register sessions
        Session::startSession();
        Session::initCookies();

        // Register required components
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
    public function setConfig($configDirs) {
        $this->config = new Config($configDirs);
        return $this;
    }

    /**
     * Gets all http request informations
     */
    public function registerHttpRequest() {
        $this->httpRequest = HttpRequest::getInstance();
    }

    /**
     * Get the template with the
     * http request
     */
    private function registerTemplate() {
        $this->template = new Template($this->httpRequest->getUrlMethodContents());
    }

    /**
     * Renders the applications frontend
     */
    private function renderApplication($args = array()) {
        $this->registerErrorHandler();
        $this->template->renderAll($args);
    }

    /**
     * Runs the application
     *
     * @return boolean
     */
    public function run() {
        $this->isRunning = true;

        // Check if application is installed already
        if (false == $this->isInstalled()) {
            $viewName = $this->template->getView()->getRealName();

            if ($viewName != self::INSTALL_VIEW) {
                $this->installApplication();
            }
            else if ($viewName == self::INSTALL_VIEW) {
                $this->template->setLayout(self::INSTALL_VIEW);
            }
        }

        try {

            // Start buffer for application
            self::startBuffer();

            // Render the frontend
            $this->renderApplication();
        }
        catch(\Exception $err) {

            // Clear just rendered content
            self::clearBuffer();

            // Change layout to exception
            $this->template->setLayout(EXCEPTION_LAYOUT);
            $this->renderApplication(array($err));
        }
    }

    /**
     * Renders a error manual with a new template
     *
     * @param  \ErrorException $err
     */
    public static function renderShutdownError($err) {
        self::clearBuffer();

        // Create new Template
        $urlMethods = HttpRequest::getInstance()->getUrlMethodContents();
        $template = new Template($urlMethods);

        /**
         * Set Exception layout and render it with
         * the exception as argument
         */
        $template->setLayout(EXCEPTION_LAYOUT);
        $template->renderAll(array($err));
    }

    /**
     * Check if the application was already
     * installed
     *
     * @return boolean
     */
    private function isInstalled() {
        return (bool)Config::getInstance()->getElementByPath('installed');
    }

    /**
     * Leads the user to the installation
     */
    private function installApplication() {

        // Redirect to the install view
        $url = array(self::INSTALL_VIEW, "step1");
        HttpHeader::redirect(UrlHelper::getBaseUrl($url));
    }

    /**
     * Starts a new buffer
     */
    public static function startBuffer() {
        ob_start();
    }

    /**
     * Ends a buffer and deletes all output
     * of it
     */
    public static function clearBuffer() {
        ob_end_flush();
        ob_clean();
    }

    /**
     * Returns the state of the application
     *
     * @return boolean
     */
    public function isRunning() {
        return $this->isRunning;
    }

    /**
     * Set the error handling function
     * to transform erros to execptions
     */
    private function registerErrorHandler() {
        $handler = new ErrorHandler();
        set_error_handler(array($handler, 'handleError'));
        register_shutdown_function(array($handler, 'handleShutdown'));
    }
}
