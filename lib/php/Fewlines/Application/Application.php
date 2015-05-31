<?php
namespace Fewlines\Application;

use Fewlines\Http\Header;

class Application extends Renderer
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
     * Bootstrap the application and
     * call the other bootstrap classes
     * from the projects (if they exist)
     *
     * @return self
     */
    public function bootstrap() {
        try {
            Buffer::start();

            // Call own bootstrap
            (new Bootstrap($this))->autoCall();

            // Call bootstrap of active project
            $project = ProjectManager::getActiveProject();

            if ($project) {
                $bootstrap = $project->bootstrap($this);
            }
        }
        catch(\Exception $err) {
            self::renderException(array($err));
        }

        return $this;
    }

    /**
     * Runs the application
     *
     * @return boolean
     */
    public function run() {
        try {
            $this->running = true;
            self::renderTemplate(DEFAULT_LAYOUT);
        }
        catch(\Exception $err) {
            self::renderException(array($err));
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

        // Set shutdown flag
        self::$shutdown = true;

        // Set 500
        Header::set(500, false);

        // Render layout
        self::renderException(array($err));
    }

    /**
     * Returns the state
     * of the application
     *
     * @return boolean
     */
    public function isRunning() {
        return $this->running;
    }
}