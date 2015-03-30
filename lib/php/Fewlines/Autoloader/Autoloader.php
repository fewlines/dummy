<?php
namespace Fewlines\Autoloader;

class Autoloader
{
    /**
     * @var string
     */
    const FILE_TYPE = 'php';

    /**
     * Simply loads a class with
     * the given path (trimmed to the basics)
     *
     * @param  string $path
     * @return boolean
     */
    public static function loadClass($path) {
        $file = str_replace('\\', DR_SP, $path) . '.' . self::FILE_TYPE;

        if (true == file_exists(LIB_PHP . DR_SP . $file) ||
            true == file_exists(LIB_PHP_TP . DR_SP . $file)) {
            require_once $file;

            if (class_exists(basename($path))) {
                return true;
            }
        }

        return false;
    }
}
