<?php
/**
 * fewlines CMS
 *
 * Description: This class builds the
 * where query
 *
 * @copyright Copyright (c) fewlines
 * @author Maurice Langlitz
 */

namespace Fewlines\Database\Where;

class Where
{
    /**
     * Holds the given wherevalues
     *
     */
    private $whereString;

    /**
     *
     * Ist the save of all where querys
     *
     */
    private $whereQuerys = array();


    /**
     * Build the query out of the array
     * @param $where
     */
    public function setValues($whereValues)
    {
        $i = 0; //counter array
        $this->whereString .= "WHERE";

        for($whereValues;$i < count($whereValues);$i++)
        {
            $values = $whereValues[$i];
            $i2 = 0; //counter the second for
            for($values;$i2 < count($values);$i2++)
            {
                echo $i2;
                if($i == 1)
                {

                    switch($i2)
                    {
                        case 0:
                        case 2: $this->whereString .= $values[$i2] . " "; break;
                        default:$this->whereString .= "'" . $values[$i2] . "' ";break;
                    }


                }
                else
                {
                    switch($i2)
                    {
                        case 1: $this->whereString .= $values[$i2] . " "; break;
                        default:$this->whereString .= "'" . $values[$i2] . "' ";break;
                    }
                }

            }
        }

        $this->whereQuerys[] = $this->whereString;
        //exit($this->whereString);
    }

    public function getString()
    {

        return $this->whereQuerys;
    }
}
?>