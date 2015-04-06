<?php
namespace Fewlines\Helper;

use Fewlines\Http\Router;

class UrlHelper extends \Fewlines\Helper\View\BaseUrl
{
    /**
     * Returns the base url
     *
     * @param  string|array $parts
     * @return string
     */
    public static function getBaseUrl($parts = "") {
        if (is_array($parts)) {
            $parts = implode("/", $parts);
        }

        return Router::getInstance()->getBaseUrl() . ltrim($parts, "/");
    }
}
