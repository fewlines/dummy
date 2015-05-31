<?php

namespace Fewlines\Component\Form\Validation\Validator;

class Maxlength extends \Fewlines\Component\Form\Validation\Validator
{
    /**
     * @param  string $value
     * @return boolean
     */
    public function validate($value) {
        if (true == is_string($value) && true == is_numeric($this->content)) {
            if (strlen($value) > intval($this->content)) {
                return false;
            }
            else {
                return true;
            }
        }

        return true;
    }
}
