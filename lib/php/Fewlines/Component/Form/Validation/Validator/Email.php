<?php

namespace Fewlines\Component\Form\Validation\Validator;

class Email extends \Fewlines\Component\Form\Validation\Validator
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
