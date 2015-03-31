<?php
namespace Fewlines\Form;

class Validation
{
    /**
     * @var array
     */
    private $options = array();

    /**
     * @var array
     */
    private $errors = array();

    /**
     * @var array
     */
    private $defaultErrors = array();

    /**
     * @var array
     */
    private $validators = array();

    /**
     * @var \Fewlines\Form\Validation\Result
     */
    private $result;

    /**
     * @param array|\Fewlines\Xml\Tree\Element $errors
     * @param array|\Fewlines\Xml\Tree\Element $options
     * @param array|\Fewlines\Xml\Tree\Element $defaultErrors
     */
    public function __construct($errors = array(), $options = array(), $defaultErrors = array()) {

        /**
         * Use xml object to set the errors
         * Otherwise use an array (Manually)
         */

        if (true == ($errors instanceof \Fewlines\Xml\Tree\Element)) {
            foreach ($errors->getChildren() as $child) {
                $this->addError($child->getName(), $child->getContent());
            }
        }
        else if (true == is_array($errors)) {
            foreach ($errors as $type => $message) {
                $this->addError($type, $message);
            }
        }

        /**
         * Use xml object to set the options
         * of the validation
         * Otherwise use an array (Manually)
         */

        if (true == ($options instanceof \Fewlines\Xml\Tree\Element)) {
            foreach ($options->getAttributes() as $type => $value) {
                $this->addOption($type, $value);
            }
        }
        else if (true == is_array($options)) {
            foreach ($options as $type => $value) {
                $this->addOption($type, $value);
            }
        }

        /**
         * Set default errors as fallback
         */

        if (true == ($defaultErrors instanceof \Fewlines\Xml\Tree\Element)) {
            foreach ($defaultErrors->getChildren() as $child) {
                $this->addError($child->getName(), $child->getContent(), true);
            }
        }
        else if (true == is_array($defaultErrors)) {
            foreach ($defaultErrors as $type => $message) {
                if (true == ($message instanceof \Fewlines\Form\Validation\Error)) {
                    $this->defaultErrors[] = $message;
                    continue;
                }

                $this->addError($type, $message, true);
            }
        }

        /**
         * Prepeare the result and
         * parse the errors
         */

        $this->result = new Validation\Result($this->errors, $this->defaultErrors);
    }

    /**
     * @param string $type
     * @param string $value
     * @throws Exception\InvalidOptionValidationTypeException
     */
    public function addOption($type, $value = "") {
        if (true == empty($type)) {
            throw new Exception\InvalidOptionValidationTypeException("No valid type given to create an option object");
        }

        $option = new Validation\Option($type, $value);
        $validator = $this->createValidatorByOption($option);

        if ($validator != false) {
            $this->validators[$validator->getType() ] = $validator;
        }

        $this->options[] = $option;
    }

    /**
     * @param  \Fewlines\Form\Validation\Option $option
     * @return \Fewlines\Form\Validation\Validator|boolean
     */
    private function createValidatorByOption(\Fewlines\Form\Validation\Option $option) {
        $class = "\\" . __NAMESPACE__ . "\\Validation\\Validator\\" . ucfirst(trim($option->getType()));

        if (true === class_exists($class)) {
            return new $class($option->getValue());
        }

        return false;
    }

    /**
     * @param string $type
     * @param string $message
     * @throws Exception\InvalidErrorValidationTypeException
     */
    public function addError($type, $message = "", $default = false) {
        if (true == empty($type)) {
            throw new Exception\InvalidErrorValidationTypeException("No valid type given to create an error object");
        }

        $error = new Validation\Error($type, $message);

        if (true == $default) {
            $this->defaultErrors[] = $error;
        }
        else {
            $this->errors[] = $error;
        }
    }

    /**
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * @param  string $type
     * @return boolean
     */
    public function hasOption($type) {
        for ($i = 0, $len = count($this->options); $i < $len; $i++) {
            if ($this->options[$i]->getType() == $type) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  string  $name
     * @return boolean
     */
    public function hasValidator($name) {
        foreach ($this->validators as $type => $validator) {
            if ($type == $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  string $type
     * @return boolean|\Fewlines\Form\Validation\Option
     */
    public function getOption($type) {
        for ($i = 0, $len = count($this->options); $i < $len; $i++) {
            if ($this->options[$i]->getType() == $type) {
                return $this->options[$i];
            }
        }

        return false;
    }

    /**
     * @param  [type] $name
     * @return boolean|\Fewlines\Form\Validation\Validator
     */
    public function getValidator($name) {
        foreach ($this->validators as $type => $validator) {
            if ($type == $name) {
                return $validator;
            }
        }

        return false;
    }

    /**
     * @param  string $value
     * @param  \Fewlines\Form\Element $element
     * @return \Fewlines\Form\Validation
     */
    public function validate($value, $element = null) {
        foreach ($this->validators as $type => $validator) {
            $this->result->addResult($type, $validator->validate($value));
        }

        return $this;
    }

    /**
     * @return array;
     */
    public function getResult() {
        return $this->result->getResult();
    }
}
