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

namespace Fewlines\Database\Update;

class Update
{
    /**
     * Holds the update values
     *
     */
    private $updateString;

    /**
     * number of the courent update function
     *
     * @var int
     */
    private $count = -1;

    /**
     * Holds all update values
     */
    private $updateQuerrys = array();

        public function setUpdate($updateValues, $tablename)
        {
            $this->count++;

            $counter = 0;
            $this->updateString = "UPDATE ";
            $this->updateString .= "`$tablename` ";
            $this->updateString .= "SET ";
            foreach($updateValues as $key => $value)
            {

                $this->updateString .= "$key ";
                $this->updateString .= " = ";
                $this->updateString .= "'$value'";
                $counter++;
                if($counter != count($updateValues))
                {
                    $this->updateString .= ",";
                }

            }
            $this->updateQuerrys[] = $this->updateString;
        }

        /**
        * return the update query as string
        *
        * @return $return
        */

        public function getString()
        {
            $return = $this->updateQuerrys[$this->count];
            return $return;
        }

}
