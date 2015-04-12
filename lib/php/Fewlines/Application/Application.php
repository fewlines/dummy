<?php
namespace Fewlines\Application;

use Fewlines\Handler\Error as ErrorHandler;
use Fewlines\Http\Request as HttpRequest;
use Fewlines\Http\Header as HttpHeader;
use Fewlines\Handler\Exception as ExceptionHandler;
use Fewlines\Http\Router;
use Fewlines\Helper\NamespaceConfigHelper;
use Fewlines\Helper\UrlHelper;
use Fewlines\Template\Template;
use Fewlines\Session\Session;
use Fewlines\Locale\Locale;

class Application
{
    /**
     * Determinates if the application
     * was shutdown
     *
     * @var boolean
     */
    private static $shutdown = false;

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
     * Simple router
     *
     * @var \Fewlines\Http\Router
     */
    private $router;

    /**
     * Inits the application components
     */
    public function __construct() {
        // Set locale
        Locale::set(DEFAULT_LOCALE);

        // Register sessions
        Session::startSession();
        Session::initCookies();
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
        $this->httpRequest = new HttpRequest;
    }

    /**
     * Init the router
     *
     * @param  \Fewlines\Http\Request $request
     */
    public function registerRouter(\Fewlines\Http\Request $request) {
        $this->router = new Router($request);
    }

    /**
     * Get the template with the
     * http request
     *
     * @param \Fewlines\Http\Router $router
     */
    private function registerTemplate(\Fewlines\Http\Router $router) {
        $this->template = new Template($router->getRouteUrlParts());
    }

    /**
     * Renders the applications frontend
     */
    private function renderApplication($args = array()) {
        $this->registerErrorHandler();

        // Render template
        $this->template->renderAll($args);
    }

    /**
     * Sets the environment
     *
     * @param string $env
     */
    public function setEnv($env) {
        Environment::set($env);
    }

    /**
     * Runs the application
     *
     * @return boolean
     */
    public function run() {
        $this->isRunning = true;

        // Start buffer for application
        self::startBuffer();

        // Register required components
        $this->registerHttpRequest();
        $this->registerRouter($this->httpRequest);
        $this->registerTemplate($this->router);

        // Get bootstrap class
        foreach (NamespaceConfigHelper::getNamespaces('php') as $key => $path) {
            $class = $path . '\\Application\\Bootstrap';
        }

        try {
            // Start bootstrap
            if (true == class_exists($class)) {
                $bootstrap = new $class($this);
            }

            // Render the components
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
        if (self::$shutdown == true) {
            exit;
        }

        // Clear previous outputs
        self::clearBuffer();

        // Create new Template
        $template = new Template(Router::getInstance()->getRouteUrlParts());

        /**
         * Set Exception layout and render it with
         * the exception as argument
         */
        $template->setLayout(EXCEPTION_LAYOUT);
        $template->renderAll(array($err));

        // Set shutdown flag
        self::$shutdown = true;
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
        ob_end_clean();
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
