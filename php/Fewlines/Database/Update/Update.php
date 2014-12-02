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
     * Holds all update values
     */
    private $updateQuerrys = array();

        public function setUpdate($updateValues, $tablename)
        {
            $counter = 0;
            $this->updateString = "UPDATE ";
            $this->updateString .= "'$tablename' ";
            $this->updateString .= "SET ";
            foreach($updateValues as $key => $value)
            {

                $this->updateString .= "'$key' ";
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

}
