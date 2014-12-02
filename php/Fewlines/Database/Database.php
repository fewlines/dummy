<?php
/**
 * fewlines CMS
 *
 * Description: This class controls
 * the connection between the
 * application and database
 *
 * @copyright Copyright (c) fewlines
 * @author Maurice Langlitz
 */

namespace Fewlines\Database;

use \Fewlines\Database\Select\Select;
use Fewlines\Database\Where\Where;
use Fewlines\Database\Update\Update;

class Database
{
    /**
     * Holds the protocol
     *
     * @var string
     */
    const PROTOCOL = 'mysqli';

    /**
     * Database object
     *
     * @var \mysqli
     */
    private $link;

    /**
     * Holds the current select instance
     *
     * @var \Fewlines\Database\Select\Select
     */
    private $select;

    /**
     * Holds the current update instance
     */
    private $update;

    /**
     * Holds the current where instance
     *
     * @var \Fewlines\Database\Where\Where
     */
    private $where;

    /**
     * Tells wether the table was set
     * or not
     *
     * @var boolean
     */
    private $wasSelected = false;

    /**
     * Create the database connection
     *
     * @param string $host
     * @param string $user
     * @param string $pw
     * @param string $db
     */
    public function __construct($host, $user, $pw, $db)
    {
        switch(self::PROTOCOL)
        {
            default:
                $this->link = new \mysqli($host, $user, $pw);
            break;
        }

        if($this->link->connect_errno)
        {
            throw new Exception\ConectException("Can't connect db!");
        }
    }

    /**
     * Selects the table
     *
     * @param string $table
     */
    public function select($table)
    {
        if(false == $this->wasSelected)
        {
            $this->wasSelected = true;

            $this->select = new Select;
            $this->select->setTable($table);
        }
        else
        {
            // Change the table
        }

        return $this;
    }

    /**
     * Make the where query
     * for all querys
     *
     * @param array $whereValues
     */

    public function where($whereValues)
    {

        $this->where = new Where;
        $this->where->setValues($whereValues);
        print_r($this->where->getString());
        return $this;
    }

    /**
     * Build the update function
     *
     * @param $updateValues
     */

    public function update($updateValues)
    {
        $this->update = new Update;

    }




}
?>