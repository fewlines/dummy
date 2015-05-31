<?php
namespace Fewlines\Helper;

use Fewlines\Application\ProjectManager;

class PathHelper
{
    /**
     * Returns a real path.
     * For example if the leading slash is missing
     * this function add's it at the and and
     * returns the new path
     *
     * @param  string $path
     * @return string
     */
    public static function getRealPath($path) {
        $path = self::normalizePath($path);
        return substr($path, -1) != DR_SP ? $path . DR_SP : $path;
    }

    /**
     * Creates a valid path from an array
     *
     * @param  array $parts
     * @return string
     */
    public static function createPath($parts) {
        return self::getRealPath(implode(DR_SP, $parts));
    }

    /**
     * Normalizes path, so all paths
     * will be the same after using this
     * function
     *
     * @param  string $path
     * @return string
     */
    public static function normalizePath($path) {
        $path = preg_replace('/\\\/', DR_SP, $path);
        return $path;
    }

    /**
     * Gets the real view no matter if there
     * is a type give or not (if not it uses
     * the default type defined)
     *
     * @param  string $view
     * @return string
     */
    public static function getRealViewFile($view) {
        $type = defined('VIEW_FILETYPE') ? VIEW_FILETYPE : 'php';
        $file = $view;
        $info = pathinfo($file);

        if ( ! array_key_exists('EXTENSION', $info)) {
            $file.= '.' . $type;
        }

        return $file;
    }

    /**
     * Returns the defined view path
     * as real path
     *
     * @param  string $view
     * @param  string $action
     * @param  string $layout
     * @return string
     */
    public static function getRealViewPath($view = '', $action = '', $layout = '') {
        $project = ProjectManager::getActiveProject();
        $path = self::getRealPath(VIEW_PATH);

        if ($project && $layout != EXCEPTION_LAYOUT) {
            $path.= self::getRealPath($project->getId());
        }
        else {
            $path.= self::getRealPath(ProjectManager::getDefaultProject()->getId());
        }

        if (false == empty($layout)) {
            $path.= self::getRealPath($layout);
        }

        if (false == empty($action) && false == empty($view)) {
            $path.= $view . DR_SP;
            $path.= self::getRealViewFile($action);
        }
        else if (false == empty($view)) {
            $path.= self::getRealViewFile($view);
        }

        return $path;
    }

    /**
     * Adds a prefix to a file name
     *
     * @param string  $path
     * @param string  $prefix
     * @param boolean $prepend
     */
    public static function addFilePrefix($path, $prefix, $prepend = true) {
        $filename = pathinfo($path, PATHINFO_FILENAME);
        $extension = '.' . pathinfo($path, PATHINFO_EXTENSION);
        $prefixPath = pathinfo($path, PATHINFO_DIRNAME) . DR_SP;

        if (false == $prepend) {
            $prefixPath.= $filename . $prefix . $extension;
        }
        else {
            $prefixPath.= $prefix . $filename . $extension;
        }

        return $prefixPath;
    }

    /**
     * Checks if the path is a absolute path
     *
     * @param  string  $path
     * @return boolean
     */
    public static function isAbsolute($path) {
        return substr($path, 0, 1) == DR_SP;
    }

    /**
     * Check if the given path
     * points to a valid view
     *
     * @param  string  $path
     * @return boolean
     */
    public static function isView($path) {
        $exp = '/' . preg_replace('/\//', '\\/', self::normalizePath(VIEW_PATH)) . '/';
        return preg_match($exp, self::normalizePath($path));
    }

    /**
     * Get's the first view path index
     * of the debug backtrace if exists
     *
     * @param  array $backtrace
     * @return number|boolean
     */
    public static function getFirstViewIndexFromDebugBacktrace($backtrace) {
        for ($i = 0, $len = count($backtrace); $i < $len; $i++) {
            if (array_key_exists('file', $backtrace[$i]) && self::isView($backtrace[$i]['file'])) {
                return $i;
            }
        }

        return false;
    }
}
