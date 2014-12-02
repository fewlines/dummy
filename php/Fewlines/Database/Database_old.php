<?php

namespace Fewlines\Database;

class Database_old
{
    /**
     * Holds the database connection
     *
     * @var Object
     */
    private $link;

    /**
     * Builds up the query to execute
     *
     * @var array
     */
    private $querys = array();

    /**
     * Make the database connetion
     *
     * @param string $host
     * @param string $user
     * @param string $pw
     * @param string $db
     */
    public function __construct($host, $user, $pw, $db)
    {
        $this->link = mysqli_connect($host, $user, $pw);
        mysqli_select_db($this->link, $db);

        if(!$this->link)
        {
            echo "Datenbanken verbindungsfehler. Error: " . mysql_error();
            exit;
        }
    }

    /**
     * Use the querry array and build the
     * mysql query an sen it ti the database
     *
     * @return string
     */
    public function queryuse($fetch = true)
    {
        $sql = "";

        foreach($this->querys as $query)
        {
            $sql .= $query;
        }

        $result = mysqli_query($this->link, $sql);
        $content = array();

        echo $sql;

        if(!$result)
        {
            echo "error: " . mysqli_error($this->link);
            return;
        }
    }

    /**
     * Write your datas into the database
     *
     * @param string $tabelname
     * @param array  $rows
     * @param array  $werte
     * @return \Fewlines\MySQLi\Database
     */
    public function insert($tabelname, $rows, $werte)
    {
        $sql = " INSERT INTO ";
        $sql .= $tabelname;
        $sql .= "(";
        $counter = 0;

        foreach($rows as $row)
        {
            $sql .= "`";
            $sql .= $row;
            $sql .= "`";
            $counter++;

            if(count($rows) != $counter)
            {
                $sql .= ",";
            }

        }

        $sql .=")";
        $sql .=" VALUES ";
        $counter = 0;
        $sql .="(";

        foreach($werte as $wert)
        {
            if(is_numeric($wert))
            {
                $sql .= "'";
                $sql .= $wert;
                $sql .= "'";
            }
            else
            {
                $sql .= '"' ;
                $sql .= $wert;
                $sql .='"';
            }

            $counter++;

            if(count($werte) != $counter)
            {
                $sql .=",";
            }
        }

        $sql .= ")";
        $this->querys[] = $sql;

        return $this;
    }

    /**
     * Build an select query and saves in $querys
     *
     * @param string $row
     * @param string $table
     * @return \Fewlines\MySQLi\Database
     */
    public function select($row, $table)
    {
        $sql = "SELECT " . $row . " FROM  " . $table . "";
        $this->querys[] = $sql;

        return $this;
    }

    /**
     * Build an where query and saves in $querrys
     * need an array of strings example test = 1
     *
     * @param string $values
     * @return \Fewlines\MySQLi\Database
     */
    public function where($values)
    {
        $sql = " WHERE ";
        $counter = 0;

        foreach($values as $string)
        {
            $sql .= $string;
            $counter++;

            if(count($values) != $counter)
            {
                $sql .= " AND ";
            }
        }

        $this->querys[]=$sql;

        return $this;
    }

    /**
     * Build an where query and saves in $querrys
     * need an array of strings example test = 1
     * $joiner is an number who set which type of join will use
     *
     * @param string $joiner
     * @param string $tables
     * @param string $strings
     * @return \Fewlines\MySQLi\Database
     */
    public function join($joiner, $tables, $strings)
    {
        switch($joiner)
        {
            case 0:
                $sql = " INNER JOIN ";
            break;

            case 1:
                $sql = " LEFT JOIN ";
            break;

            case 3:
                $sql = " RIGHT JOIN ";
            break;
        }

        $sql .= $tables;
        $sql .= " ON ";
        $sql .= $strings;

        $this->querys[] = $sql;

        return $this;
    }

    /**
     * Build an truncate or delet query and saves in $querrys
     * $numbers = 0 is deleted or truncate
     *
     * @param integer $number
     * @param string  $table
     * @return \Fewlines\MySQLi\Database
     */
    public function remove($number, $table)
    {
        if($number == 0)
        {
            $sql = " DELETE FROM ";
        }
        else
        {
            $sql = " TRUNCATE ";
        }

        $sql .= "`" . $table . "`";
        $this->querys[] = $sql;

        return $this;
    }

    /**
     * Build an update query and saves in $querrys
     * need an array of strings example test = 1
     *
     * @param string $tabel
     * @param string $values
     * @return \Fewlines\MySQLi\Database
     */
    public function update($table, $values)
    {
        $sql = "UPDATE ";
        $sql .= "`" . $table . "`";
        $sql .= " SET ";
        $counter = 0;

        foreach($values as $string)
        {
            $sql .= $string;
            $counter++;

            if(count($values) != $counter)
            {
                $sql .= ", ";
            }

        }

        $this->querys[] = $sql;

        return $this;
    }

    /**
     * Build an update query and saves in $querrys
     * need an string
     *
     * @param string $table
     * @return \Fewlines\MySQLi\Database
     */
    public function group($table)
    {
        $sql = " GROUP BY " . $table;
        $this->querys[] = $sql;

        return $this;
    }

    /**
     * Build an update query and saves in $querrys
     * need an array of tabeles
     * and who sort
     *
     * @param string $order
     * @param array  $table
     * @return object
     */
    public function order($tables, $order = "DESC")
    {
        $counter = 0;
        $sql = " ORDER BY ";

        foreach($tables as $table)
        {
            $sql .= "'" . $table . "'";
            $counter++;

            if(count($tables) != $counter)
            {
                $sql .= ", ";
            }
        }

        $sql .= $order;
        $this->querys[] = $sql;

        return $this;
    }



}
?>