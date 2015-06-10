<?php
namespace Fewlines\Core\Template;

use Fewlines\Core\Http\Header as HttpHeader;
use Fewlines\Core\Helper\PathHelper;
use Fewlines\Core\Helper\ArrayHelper;
use Fewlines\Core\Template\Template;

class Renderer
{

    /**
     * A array of all md5 hashes to save
     * performmance
     *
     * @var array
     */
    private static $md5VarHashmap = array('file', 'config', 'varname', 'content', 'html');

    /**
     * The result of the controller to display
     * in the view
     *
     * @var string
     */
    private $controller;

    /**
     * Init the renderer
     */
    public function __construct() {
        $this->initHashmap();
    }

    /**
     * Init hashmap for a clean
     * html render
     */
    private function initHashmap() {
        // Calculate hashmaps (if they weren't just calculated)
        if (false == ArrayHelper::isAssociative(self::$md5VarHashmap)) {
            for ($i = 0; $i < count(self::$md5VarHashmap); $i++) {
                self::$md5VarHashmap[self::$md5VarHashmap[$i]] = md5(self::$md5VarHashmap[$i]);
                unset(self::$md5VarHashmap[$i]);
            }
        }
    }

    /**
     * Includes a php file and returns the content
     * output with a buffer.
     *
     * Using md5 hashed variables to avoid override of
     * the config variables from the user. Looks weird
     * but to save performance, the md5 hashes will only
     * be calculated once
     *
     * @param  string $file
     * @param  array  $config
     * @return string
     */
    public function getRenderedHtml($file, $config = array()) {
        ob_start();

        // Cache old vars
        ${self::$md5VarHashmap['file']}   = $file;
        ${self::$md5VarHashmap['config']} = $config;

        // Delete old variables
        unset($file, $config);

        // Define config variables
        foreach (${self::$md5VarHashmap['config']}  as ${self::$md5VarHashmap['varname']} => ${self::$md5VarHashmap['content']}) {
            ${self::$md5VarHashmap['varname']} = (string) ${self::$md5VarHashmap['varname']};
            ${${self::$md5VarHashmap['varname']}} = ${self::$md5VarHashmap['content']};
        }

        // Include the cached file
        include ${self::$md5VarHashmap['file']};

        // Get the output of the buffer form the included file
        ${self::$md5VarHashmap['html']} = ob_get_clean();

        // Return the saved buffer
        return ${self::$md5VarHashmap['html']};
    }

    public function renderLayout() {
        $template = Template::getInstance();
        $view = $template->getView();

        // Call view controller from view (if exists)
        $this->controller = $view->initController();

        // Set layout
        $layout = $template->getLayout();

        if ($this->layout->isDisabled()) {
            if (is_string($this->controller)) {
                echo $this->controller;
            }
            else {
                $this->renderView();
            }
        }
        else {
            // Render layout
            echo $this->getRenderedHtml($layout->getPath());
        }
    }

    /**
     * Renders a view and returns the content
     * A optional view if possible. If no view is
     * given. The view of the layout will be
     * taken.
     *
     * @param  string $viewPath
     */
    public function renderView($viewPath = '') {
        // Get current layout
        $template = Template::getInstance();
        $view = $template->getView();
        $layout = $template->getLayout();

        if (empty($viewPath)) {
            if ( ! $view->isRouteActive()) {
                // Get view and action
                $file = $view->getPath();
                $action = $view->getAction();

                if (is_string($this->controller)) {
                    // Output rendered html from the return of the controller
                    echo $this->controller;
                }
                else {
                    // Output rendered html (view)
                    echo $this->getRenderedHtml($file);
                }
            }
            else {
                if(is_string($this->controller)) {
                    echo $this->controller;
                }
                else {
                    echo '';
                }
            }
        }
        else {
            $file = PathHelper::getRealViewPath($viewPath, '', $layout->getName());

            if (file_exists($file)) {
                $file = $this->getRenderedHtml($file);
            }

            return $file;
        }
    }

    /**
     * Returns the content of
     * a rendered element
     *
     * @param  string $viewPath
     * @param  array  $config
     * @param  string $wrapper
     * @return string
     */
    public function render($viewPath, $config = array(), $wrapper = '') {
        $view = PathHelper::getRealViewPath(ltrim($viewPath, DR_SP), '', Template::getInstance()->getLayout()->getName());
        $path = $view;

        // Handle relative path
        if ( ! PathHelper::isAbsolute($viewPath)) {
            $backtrace = debug_backtrace();
            $viewIndex = PathHelper::getFirstViewIndexFromDebugBacktrace($backtrace);

            if(false === $viewIndex) {
                throw new Exception\NoViewOriginFoundException(
                    'The view "' . $view . '" could not be
                    include. You can render relative path\'s
                    only from an other view.'
                );
            }

            $file = PathHelper::normalizePath($backtrace[$viewIndex]['file']);
            $dir = pathinfo($file, PATHINFO_DIRNAME);

            $path = PathHelper::getRealViewFile(PathHelper::normalizePath($dir . DR_SP . $viewPath));
            $view = PathHelper::normalizePath(realpath($path));

            if ($view == $file) {
                throw new Exception\RenderRecursionException(
                    'The view "' . $view . '" is including itself (Recursion).'
                );

                exit;
            }
        }

        /**
         * Default check & render
         */

        if ( ! $view || ! file_exists($view)) {
            if ( ! $view) {
                $view = $path;
            }

            throw new Exception\ViewIncludeNotFoundException(
                'The view "' . $view . '" was not found
                and could not be included'
            );
        }

        $content = $this->getRenderedHtml($view, $config);

        if ( ! empty($wrapper)) {
            $content = sprintf($wrapper, $content);
        }

        return $content;
    }

    /**
     * Render a component and outputs
     * the content of it
     *
     * @param  string $viewPath
     * @param  array  $config
     * @param  string $wrapper
     * @return string
     */
    public function insert($viewPath, $config = array(), $wrapper = '') {
        echo $this->render($viewPath, $config, $wrapper);
    }
}
