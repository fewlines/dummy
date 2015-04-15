<?php
namespace Fewlines\Http;

class Request
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

    /**
     * Init the request and set is
     * as singlteon
     */
    public function __construct() {
        $this->response = new Request\Response;
    }

    /**
     * Returns the response
     * of this request
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
     * @return string
     */
    public function getProtocol() {
        return stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';
    }

    /**
     * @return string
     */
    public function getHost() {
        return $_SERVER['HTTP_HOST'];
    }

    /**
     * @return string
     */
    public function getUrl() {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * @return string
     */
    public function getFullUrl() {
        return $this->getProtocol() . $this->getHost() . $this->getUrl();
    }

    /**
     * @return string
     */
    public function getHttpMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }
}