<?php
namespace Fewlines\Application;

use Fewlines\Handler\Error as ErrorHandler;
use Fewlines\Http\Request as HttpRequest;
use Fewlines\Http\Header as HttpHeader;
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
     * The environment for a application
     * instance
     *
     * @var \Fewlines\Application\Environment
     */
    private static $environment;

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
        $this->registerErrorHandler();
        $this->setConfig();

        // Create environment
        self::$environment = new Environment;

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
     */
    public function setConfig() {
        $this->config = Config::getInstance();
    }

    /**
     * Gets all http request informations
     */
    public function registerHttpRequest() {
        $this->httpRequest = new HttpRequest;
    }

    /**
     * Init the router
     */
    public function registerRouter() {
        $this->router = Router::getInstance();
    }

    /**
     * Get the template with the
     * http request
     */
    private function registerTemplate() {
        $this->template = Template::getInstance();
    }

    /**
     * Renders the applications frontend
     */
    private function renderApplication($args = array()) {
        $this->template->setLayout(DEFAULT_LAYOUT);
        $this->template->renderAll($args);
    }

    /**
     * @return \Fewlines\Application\Environment
     */
    public static function getEnvironment() {
        return self::$environment;
    }

    /**
     * @return \Fewlines\Application\Environment
     */
    public static function getEnv() {
        return self::getEnvironment();
    }

    /**
     * Runs the application
     *
     * @return boolean
     */
    public function run() {
        $this->isRunning = true;

        // Start buffer for application output
        self::startBuffer();

        try {
            // Set inital environment types
            self::$environment->setTypes(array('production:live', 'staging:testing', 'development:local'));

            self::$environment->addUrlPattern('production', '/\.local/');
            // self::$environment->addUrlPattern('staging', '/\.local/');
            self::$environment->addHostname('development', 'Davide-PC');
            // self::$environment->addHostname('development', 'daria-mini');

            var_dump(self::$environment->isLive());

            // Get bootstrap class
            foreach (NamespaceConfigHelper::getNamespaces('php') as $key => $path) {
                $class = $path . '\\Application\\Bootstrap';
            }

            // Start bootstrap
            if (true == class_exists($class)) {
                $bootstrap = new $class($this);
            }

            // Register required components
            $this->registerHttpRequest();
            $this->registerRouter();
            $this->registerTemplate();

            // Render the template
            $this->renderApplication();
        }
        catch(\Exception $err) {
            // Clear just rendered content
            self::clearBuffer();

            // Create new template
            $template = Template::getInstance();

            // Change layout to exception
            $template->setLayout(EXCEPTION_LAYOUT);
            $template->renderAll(array($err));
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

        // Set 500
        HttpHeader::set(500, false);

        // Create new Template
        $template = Template::getInstance();

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
        while(false == empty(ob_get_contents())) {
            ob_end_clean();
        }
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
