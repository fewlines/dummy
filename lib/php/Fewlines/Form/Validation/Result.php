<?php
namespace Fewlines\Form\Validation;

class Result
{
    /**
     * The result of the validation
     * saved as associative array
     *
     * @var array
     */
    private $validation;

    /**
     * The error list to replace with the
     * matching type (validator)
     *
     * @var array
     */
    private $errors;

    /**
     * Default errors handles the fallback
     * for all uncatched errors
     *
     * @var array
     */
    private $defaultErrors;

    /**
     * Parse the initial errors to operate with
     * later after the user fetches the result.
     * Also set the optional default errors for
     * a fallback if no error is given
     *
     * @param array $errors
     * @param array $defaultErrors
     */
    public function __construct($errors, $defaultErrors = array()) {
        $this->errors = $errors;
        $this->defaultErrors = $defaultErrors;
    }

    /**
     * Adds a result to the current validation
     * made of the type (validator) and the result
     * which should always be a boolean that tells
     * if the validation of this validator passed
     *
     * @param string  $type
     * @param boolean $result
     */
    public function addResult($type, $result) {
        $this->validation[$type] = $result;
    }

    /**
     * @param  string $type
     * @return \Fewlines\Form\Validation\Error
     */
    private function getErrorByType($type) {
        // Check for given errors
        for ($i = 0, $len = count($this->errors); $i < $len; $i++) {
            $error = $this->errors[$i];

            if ($error->getType() == $type) {
                return $error;
            }
        }

        // Fallback to default errors given
        for ($i = 0, $len = count($this->defaultErrors); $i < $len; $i++) {
            $error = $this->defaultErrors[$i];

            if ($error->getType() == $type) {
                return $error;
            }
        }

        // Create new error to notify the user no error was found
        return new Error($type, ucfirst($type) . ': Error message not found');
    }

    /**
     * Simply connects the result type and
     * the error message and returns a result
     * in form of an associative array
     *
     * @return array
     */
    public function getResult() {
        $result = array();

        // Collect errors
        foreach ($this->validation as $type => $isValid) {
            $error = $this->getErrorByType($type);

            if (true == ($error instanceof \Fewlines\Form\Validation\Error) && false == $isValid) {
                $result[$type] = $error->getMessage();
            }
        }

        return $result;
    }
}
