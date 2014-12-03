<?php
/**
 * fewlines CMS
 *
 * Description: This class holds the
 * selected table
 *
 * @copyright Copyright (c) fewlines
 * @author Maurice Langlitz
 */
namespace Fewlines\Database\Select;

class Select
{
    /**
     * Holds the given tablename
     *
     * @var string
     */
    private $table;

    /**
     *
     * Holds the select function
     *
     * @param $table
     * @param $rows
     */
    private $selectQuerry = array();

    public function setSelect($table, $rows)
    {
        $data = " ";
        $x = 0;
        $this->table = $table;

        $data .= "SELECT ";
        for($i = 0; $i != count($rows);$i++)
        {
            $data .= "$rows[$i]";
            $x++;
            if($x != count($rows))
            {
                $data .= ",";
            }
        }
        $data .= " FROM ";
        $data .= $this->table;

        $this->selectQuerry[] = $data;


    }

    public function getString()
    {

        $return = $this->selectQuerry[0];
        return $return;
    }

    public function getTable()
    {
        return $this->table;
    }
}

?>