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

//use Fewlines\Database\Fetch\Fetch;
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
     * holds the last query
     *
     * @var String
     */
    private $query = array();

    /**
     * Holds the current select instance
     *
     * @var \Fewlines\Database\Select\Select
     */
    private $select;

    /**
     * Holds the current Fetch instance
     *
     * @var \Fewlines\Database\Fetch\Fetch
     */

    private $fetch;

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
     * @param array $rows
     */
    public function select($table, $rows)
    {
        $this->query = array(); // truncate the array

        $this->select = new Select();
        $this->select->setSelect($table, $rows);
        $this->query[] = $this->select->getString();
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
        $this->query[] = $this->where->getString();
        return $this;


    }

    /**
     * Build the update function
     *
     * @param $updateValues
     */

    public function update($updateValues)
    {
        $this->query = array(); // truncate the array

        $this->update = new Update;
        $tablename = $this->select->getTable();
        $this->update->setUpdate($updateValues, $tablename);
        $this->query[] = $this->update->getString();
        return $this;

    }

    public function fetch()
    {
        //$this->fetch = new Fetch();
        //$this->fetch->setFetch();
    }

    public function getQuery()
    {
        $return = implode($this->query);
        return $return;
    }

}
?>