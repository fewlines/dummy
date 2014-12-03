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
     * @var string
     */
    private $whereString;

    /**
     * number of the courent where function
     *
     * @var int
     */
    private $count = -1;

    /**
     * Holds the where queries
     *
     * @var array
     */
    private $whereQuerys = array();

    /**
     * Build the query out of the array
     * @param $where
     */
    public function setValues($whereValues)
    {
        $this->count++;
        $this->whereString .= "WHERE ";

        for($i = 0; $i < count($whereValues); $i++)
        {
            $values = $whereValues[$i];

            for($x = 0; $x < count($values); $x++)
            {
                if($i == 1)
                {
                    switch($x)
                    {
                        case 0:
                        case 2:
                            $this->whereString .= $values[$x] . " ";
                        break;

                        default:
                            $this->whereString .= "'" . $values[$x] . "' ";
                        break;
                    }
                }
                else
                {
                    switch($x)
                    {
                        case 1:
                            $this->whereString .= $values[$x] . " ";
                        break;

                        default:
                            $this->whereString .= "'" . $values[$x] . "' ";
                        break;
                    }
                }
            }
        }

        $this->whereQuerys[] = $this->whereString;
    }

    /**
     * return the where query as string
     *
     * @return $return
     */

    public function getString()
    {
        $return = $this->whereQuerys[$this->count];
        return $return;
    }
}
?>