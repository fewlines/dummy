<?php

$db = new DBTest();

/**
 * Insert functions
 */
$insertValues = array(
				// Spalte      Wert (string, int)
				"vorname"      => "davide",
				"nachname"     => "perozzi"
				"arrayImplode" => array("value1", "value2") // to string: value1, value2
			);

$db->select('table')->insert($insertValues)->save(); // chainable
$db->insertInto('table', $insertValues); 			 // Shortcut (not chainable)


/**
 * Update
 */
$updateValues = array(
		"username" => "davide",
		"nachname" => "perozzi",
		"passowrd" => "potatoe"
	);

$whereValues = array(
		array(
			"colname", // Collumname
			"=",       // Operator
			"value"    // value
		),
		array(
			"OR|AND|...", // Operator to link the conditions
					      // IF empty OR first value NOT REQUIRED, IF empty and NOT first value THROW EXCEPTION

			"colname",    // Collumname
			"<=",         // Operator
			"value"	      // value

		),
    		array(
                "OR|AND|...", // Operator to link the conditions
                              // IF empty OR first value NOT REQUIRED, IF empty and NOT first value THROW EXCEPTION

                "colname",    // Collumname
                "<=",         // Operator
                "value"	      // value

	));

$db->select('table')->update($updateValues)->where($whereValues)->save();

/**
 * Delete
 */
$db->select('table')->delete()->where($whereValues)->save();
$db->select('table')->update($updateValues)->where($whereValues)->delete()->where($whereValues)->save(); // Chained update and delete

/**
 * Simple fetch
 */
$db->select('table')->where($whereValues)->fetchAll();
$db->select('table')->where($whereValues)->fetchRow();
$db->select('table')->where($whereValues)->fetchWhere($whereValues);

/**
 * User queries
 */
$db->query("SELECT * FROM `table`")->query("DELETE FROM `tabkle` WHERE vorname='davide'")->exec();
$db->query("SELECT * FROM `table`")->query("DELETE FROM `tabkle` WHERE vorname='davide'")->fetchAll();
$db->multiQuery("
		SELECT * FROM `table`;
		DELETE FROM `tabkle` WHERE vorname='davide';
	")->fetchAll();