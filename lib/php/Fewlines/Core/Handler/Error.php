<?php
namespace Fewlines\Core\Handler;

use Fewlines\Core\Application\Application;
use Fewlines\Core\Application\ProjectManager;

class Error
{
    /**
     * @var string
     */
    const ERROR_FNC = 'handleError';

    /**
     * @var string
     */
    const SHUTDOWN_FNC = 'handleShutdown';

    /**
     * The message which appears if
     * the application shuts down
     *
     * @var string
     */
    const SHUTDOWN_ERROR_MESSAGE = "<b>Shutdown</b> by an error";

    /**
     * Handles the error
     *
     * @param  int    $errno
     * @param  string $errstr
     * @param  string $errfile
     * @param  int    $errline
     * @throws ErrorException IF $exit is true
     */
    public function handleError($errno, $errstr, $errfile, $errline) {
        $exit = false;
        $type = "";

        if (error_reporting() & $errno) {
            switch ($errno) {
                case E_USER_ERROR:
                    $type = 'FatalError';
                    $exit = true;
                    break;

                case E_USER_WARNING:
                case E_WARNING:
                    $type = 'Warning';

                    // Output warning with some styles :)
                    echo '<div class="error-handler-warning"
                                style="
                                    position: relative;
                                    display: block;
                                    background: #2E2E2E;
                                    padding: 10px;
                                    margin: 10px;
                                    color: white;
                                    font-family: Arial;
                                    font-size: 14px;
                                "
                            >' .
                            '<div
                                style="
                                    position: absolute;
                                    top: 5px;
                                    right: 5px;
                                    font-size: 11px;
                                    color: gray;
                                    text-transform: uppercase;
                                "
                            >Warning</div>' .
                            '<div class="error-handler-warning-message"
                                style="
                                    font-weight: bold;
                                    font-size: 16px;
                                    color: #E4BE0C;
                                "
                            >' . $errstr . '</div>' .
                            '<div class="error-handler-warning-file-line"
                                style="
                                    color: #F1F1F1;
                                "
                            >' .
                                '<div class="error-handler-warning-file"
                                    style="
                                        display: inline-block;
                                    "
                                >' . $errfile . '&nbsp;:</div>' .
                                '<div class="error-handler-warning-line"
                                    style="
                                        display: inline-block;
                                        color: #E4BE0C;
                                    "
                                >&nbsp;' . $errline . '</div>' .
                            '</div>' .
                         '</div>';
                    break;

                case E_USER_NOTICE:
                case E_NOTICE:
                case @E_STRICT:
                    $type = 'Notice';
                    $exit = true;
                    break;

                case @E_RECOVERABLE_ERROR:
                    $type = 'Catchable';
                    break;

                default:
                    $type = 'UnknownError';
                    $exit = true;
                    break;
            }
        }

        if (true == $exit) {
            $className = '\\' . ProjectManager::getDefaultProject()->getNsName();
            $className.= '\Handler\Error\Exception\\' . $type . 'Exception';

            throw new $className($errstr, 0, $errno, $errfile, $errline);
        }
    }

    public function handleShutdown() {
        $isError = false;
        $type = "UnknownError";

        if ($error = error_get_last()) {
            switch ($error['type']) {
                case E_ERROR:
                    $type = "FatalError";
                    $isError = true;
                case E_CORE_ERROR:
                    $type = "CoreError";
                    $isError = true;
                case E_COMPILE_ERROR:
                    $type = "CompileError";
                    $isError = true;
                case E_USER_ERROR:
                    $type = "UserError";
                    $isError = true;
                case E_PARSE:
                    $type = "ParseError";
                    $isError = true;
                    break;
            }

            if ($isError) {
                // Create new exception
                $className = '\\' . ProjectManager::getDefaultProject()->getNsName();
                $className.= '\Handler\Error\Exception\Shutdown\\' . $type . 'Exception';
                $exception = new $className;

                if (false == is_null($exception)) {
                    $message = array_key_exists('message', $error) ? $error['message'] : $type;
                    $exception->setMessage(self::SHUTDOWN_ERROR_MESSAGE . ": " . $message);

                    // Define position
                    if (array_key_exists('line', $error) && array_key_exists('file', $error)) {
                        $exception->setFile($error['file']);
                        $exception->setLine($error['line']);
                    }

                    // Render new application after wipeout
                    Application::renderShutdownError($exception);
                }
            }
        }
    }
}
