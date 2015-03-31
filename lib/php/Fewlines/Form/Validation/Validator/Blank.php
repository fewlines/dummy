<?php
namespace Fewlines\Form\Validation\Validator;

class Blank extends \Fewlines\Form\Validation\Validator
{
    /**
     * @param  string $value
     * @return boolean
     */
    public function validate($value) {
        if (true == $this->content && false == is_array($value)) {
            return trim(preg_replace('/ |\t|\r|\r\n/', '', $value)) != '';
        }

        return true;
    }
}
