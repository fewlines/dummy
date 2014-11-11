<?php
class Database
{


    public function __construct($name, $pw, $user)
    {
        $db = mysqli_connect("localhost",$name, $pw, $user);
        if(!$db)
        {
            mysql_error();
            echo("Datenbanken verbindungsfehler");
        }
    }

    public function select($spalte, $tabelle, $where)
    {
        $spalte = "test1";
        $tabelle = test2;
        $where = test3;
        $sql = "SELECT '.$spalte.' FROM  '.$tabelle.' WEHERE '.$where.'";
        echo($sql);
        //$db = mysql_query();
    }
}
?>