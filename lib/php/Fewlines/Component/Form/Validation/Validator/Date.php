<?php
namespace Fewlines\Component\Form\Validation\Validator;

class Date extends \Fewlines\Component\Form\Validation\Validator
{
    /**
     * @param  string $value
     * @return boolean
     */
    public function validate($value) {
        if (false == empty($this->content) && true == is_string($this->content)) {
            $date = \DateTime::createFromFormat($this->content, $value);

            return $date && $date->format($this->content) == $value;
        }
        else if (true == $this->content) {
            $time = strtotime($value);

            if (false != $time) {
                $date = explode("-", date("Y-m-d", $time));
                list($year, $month, $day) = $date;

                return checkdate($month, $day, $year);
            }
            else {
                return false;
            }
        }

        return true;
    }
}
