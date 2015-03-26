<?php
namespace Fewlines\Template;

use Fewlines\Template\View;
use Fewlines\Http\Request as HttpRequest;

class Layout
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $path;

    /**
     * Tells if the layout should be
     * rendered or not
     *
     * @var boolean
     */
    private $disabled = false;

    /**
     * @param string $name
     * @param string $path
     * @param array  $routeUrlParts
     */
    public function __construct($name, $path, $routeUrlParts) {
        $this->name = $name;
        $this->path = $path;
    }

    /**
     * @param boolean $isDisabled
     */
    public function disable($isDisabled) {
        $this->disabled = $isDisabled;
    }

    /**
     * @return boolean
     */
    public function isDisabled() {
        return $this->disabled;
    }

    /**
     * @return string
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return \Fewlines\Template\View
     */
    public function getView() {
        return $this->view;
    }
}
