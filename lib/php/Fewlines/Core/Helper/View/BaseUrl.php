<?php
namespace Fewlines\Core\Helper\View;

use Fewlines\Core\Http\Request as HttpRequest;
use Fewlines\Core\Helper\UrlHelper;

class BaseUrl extends \Fewlines\Core\Helper\AbstractViewHelper
{
    public function init() {
    }

    /**
     * Returns the baseurl with the optional
     * part, which will be appended
     *
     * @param  string|array $parts
     * @return string
     */
    public function baseUrl($parts = "") {
        return UrlHelper::getBaseUrl($parts);
    }
}
