<?php
namespace Fewlines\Http;

use Fewlines\Http\Router as Router;

class Request extends Router
{
    /**
     * Holds the instace for singleton getter
     *
     * @var Fewlines\Http\Request
     */
    private static $instance;

    /**
     * @var \Fewlines\Http\Request\Response
     */
    private $response;

    public function __construct() {
        $this->initRouter();
        $this->response = new Request\Response;

        self::$instance = $this;
    }

    /**
     * Get the instance
     *
     * @return \Fewlines\Http\Request
     */
    public static function getInstance() {
        if (is_null(self::$instance)) {
            new self();
        }

        return self::$instance;
    }

    /**
     * Returns the response of this request
     *
     * @return \Fewlines\Http\Request\Response
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     * Returns all parameters given
     * by the router (get or post)
     *
     * @param  string $type
     * @return array
     */
    public function getParams($type = 'get') {
        $type = strtolower($type);
        $parameters = array();

        switch ($type) {
            case 'post':
                $parameters = $this->getUrlPartsPOST();
                break;

            default:
            case 'get':
                $parameters = $this->getUrlPartsGET();
                break;
        }

        return $parameters;
    }

    /**
     * Returns a parameters value
     * by the given type and name
     *
     * @param string $name
     * @param string $type
     * @return *
     */
    public function getParam($name, $type = 'get') {
        $type = strtolower($type);
        $parameters = $this->getParams($type);

        return array_key_exists($name, $parameters) ? $parameters[$name] : NULL;
    }

    /**
     * Returns all methods of the route
     * with content
     *
     * @return array|\Fewlines\Http\Router\Routes\Route
     */
    public function getUrlMethodContents() {
        return $this->getRouteUrlParts();
    }

    /**
     * @return string
     */
    public function getHttpMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }
}