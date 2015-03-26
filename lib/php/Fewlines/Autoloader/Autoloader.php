<?php
namespace Fewlines\Autoloader;

class Autoloader
{
    /**
     * Simply loads a class with
     * the given path (trimmed to the basics)
     *
     * @param  string $path
     * @return boolean
     */
    public static function loadClass($path) {
        $file = str_replace('\\', DR_SP, $path) . '.php';

        if (file_exists(FEWLINES_PHP . DR_SP . $file)) {
            require_once $file;

            if (class_exists(basename($path))) {
                return true;
            }
        }

        return false;
    }
}
