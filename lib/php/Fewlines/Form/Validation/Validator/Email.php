<?php

namespace Fewlines\Form\Validation\Validator;

class Email extends \Fewlines\Form\Validation\Validator
{
    /**
     * @param  string $value
     * @return boolean
     */
    public function validate($value) {
        if (true == $this->content) {
            return (bool)filter_var($value, FILTER_VALIDATE_EMAIL);
        }

        return true;
    }
}
