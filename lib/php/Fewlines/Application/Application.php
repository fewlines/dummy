<?php
namespace Fewlines\Application;

use Fewlines\Template\Template;

class Application
{
    /**
     * Determinates if the application
     * was shut down
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
    private $running = false;

    /**
     * Renders the applications frontend
     */
    private function renderApplication($args = array()) {
        Template::getInstance()->setLayout(DEFAULT_LAYOUT)->renderAll($args);
    }

    /**
     * Bootstrap the application and
     * call the other bootstrap classes
     * from the projects (if they exist)
     *
     * @return self
     */
    public function bootstrap() {
        // Call own bootstrap
        (new Bootstrap($this))->autoCall();

        // Call bootstrap of active project
        $project = ProjectManager::getActiveProject();

        if ($project) {
            $bootstrap = $project->bootstrap($this);
        }

        return $this;
    }

    /**
     * Runs the application
     *
     * @return boolean
     */
    public function run() {
        $this->running = true;

        // Start buffer for application output
        self::startBuffer();

        try {
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

        // Set 500
        HttpHeader::set(500, false);

        // Clear previous outputs
        self::clearBuffer();

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
        while( ! empty(ob_get_contents())) {
            ob_end_clean();
        }
    }

    /**
     * Returns the state of the application
     *
     * @return boolean
     */
    public function isRunning() {
        return $this->running;
    }
}
