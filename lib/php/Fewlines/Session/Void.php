<?php
namespace Fewlines\Session;

class Void
{
    /**
     * Catch a method call
     *
     * @param  string $name
     * @param  array  $args
     * @return boolean
     */
    public function __call($name, $args) {
        return false;
    }

    /**
     * Catch a property fetch
     *
     * @param  string $var
     * @return boolean
     */
    public function __get($var) {
        return false;
    }
}
