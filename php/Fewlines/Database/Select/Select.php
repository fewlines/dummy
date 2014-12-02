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

    public function __set($table)
    {
        $this->table = $table;
    }

    public function __get()
    {
        return $this->$table;
    }
}

?>