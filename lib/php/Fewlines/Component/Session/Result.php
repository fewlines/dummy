<?php
namespace Fewlines\Component\Session;

class Result
{
    /**
     * Holds a cookie as result
     *
     * @var Session
     */
    private $session;

    /**
     * Holds a cookie as result
     *
     * @var Cookie\Cookie
     */
    private $cookie;

    /**
     * Sets a session (native) as result
     *
     * @param $session Cookie\Session
     */
    public function setSession(Cookie\Session $session) {
        $this->session = $session;
    }

    /**
     * Sets a cookie (native) as result
     *
     * @param Cookie\Cookie $cookie
     * @internal param Cookie\Cookie $session
     */
    public function setCookie(Cookie\Cookie $cookie) {
        $this->cookie = $cookie;
    }

    /**
     * Resturns the session (native) if
     * a session was set
     *
     * @return Session
     */
    public function getSession() {
        return is_null($this->session) ? new Void : $this->session;
    }

    /**
     * Returns the cookie (native) if
     * a cookie was set
     *
     * @return Cookie\Cookie
     */
    public function getCookie() {
        return is_null($this->cookie) ? new Void : $this->cookie;
    }

    /**
     * Check if a cookie is given
     *
     * @return boolean
     */
    public function isCookie() {
        return !is_null($this->cookie);
    }

    /**
     * Check if a session is given
     *
     * @return boolean
     */
    public function isSession() {
        return !is_null($this->session);
    }

    /**
     * Returns if any result was set
     *
     * @return boolean
     */
    public function isEmpty() {
        return is_null($this->cookie) && is_null($this->session);
    }
}
