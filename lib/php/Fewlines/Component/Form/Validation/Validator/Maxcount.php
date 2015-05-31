<?php
namespace Fewlines\Component\Form\Validation\Validator;

class Maxcount extends \Fewlines\Component\Form\Validation\Validator
{
    /**
     * @param  array|string $value
     * @return boolean
     */
    public function validate($value) {
        if (true == is_numeric($this->content)) {
            if (true == is_array($value)) {
                if (count($value) > intval($this->content)) {
                    return false;
                }
            }
            else {
                if (is_string($value) && $value == '') {
                    return true;
                }

                return false;
            }
        }

        return true;
    }
}
