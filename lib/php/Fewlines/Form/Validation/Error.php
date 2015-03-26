<?php
namespace Fewlines\Form\Validation;

class Error
{
    /**
     * The type of the error.
     * E.g. regex
     *
     * @var string
     */
    private $type;

    /**
     * The message of the error
     * which wil be displayed
     *
     * @var string
     */
    private $message;

    /**
     * @param string $type
     * @param string $message
     */
    public function __construct($type, $message = "") {
        $this->type = $type;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getMessage() {
        return $this->message;
    }
}
