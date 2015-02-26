<?php

namespace Fewlines\Controller\View;

use Fewlines\Session\Session;
use Fewlines\Crypt\Crypt;
use Fewlines\Helper\PathHelper;
use Fewlines\Database\Database;
use Fewlines\Form\Form;
use Fewlines\Locale\Locale;

class Index extends \Fewlines\Controller\Template
{
	public function testformAction()
	{
		$config = $this->getConfig()->getElementByPath("form/install");
		$form   = new Form($config);

		pr($form);
	}

	public function indexAction()
	{
		/**
		 * Session handling
		 */

		//$session = new Session("testCookie", "test");
		$cook = Session::get('testCookie');

		if($cook->isSession())
		{
			echo "SESS: " . $cook->getSession() . "<br />";
		}

		if($cook->isCookie())
		{
			echo "COOK: " . $cook->getCookie()->getContent() . "<br />";
		}

		/**
		 * Config handling
		 */

		$config  = $this->getConfig(); // Config::GetInstance()
		$host    = $config->getElementByPath("database/host");
		$version = $config->getElementsByPath("application/version");

		/**
		 * Database handling
		 */

		$db              = new Database;
		// $resultInsert    = $db->select("tabletest")->insert(array("content" => "cont", "password" => md5("pass" . rand(0,100))))/*->execute()*/;
		// $resultUpdate    = $db->select("tabletest")->update(array("content" => "updatedcontent", "password" => "updatedpassword"))->where(array("id", "=", 2), "OR")->where(array("id", "=", 3))/*->execute()*/;
		// $resultDelete    = $db->select("tabletest")->where(array("id", ">", 0))->delete()/*->execute()*/;
		// $resultFetch     = $db->select("tabletest", "*")->where(array("id", ">", 1))->where(array("id", "<", 4))->limit(0,5)->fetchAll();
		// $resultTruncate  = $db->select("tabletest")->truncate()/*->execute()*/;
		// $resultLikeFetch = $db->select("tabletest", "*")->where(array("content", "LIKE", "%co%"))->fetchAll();
		// $resultDropTable = $db->select("tabletest")->drop()/*->execute()*/;

		/*$resultTableCreate = $db->createTable("tabletest2",
				array(
					"id" => array(
						"type"          => "int",
						"autoIncrement" => true,
						"notNull"       => true,
						"index"         => "primary"
					),
					"name" => array(
						"type"          => "varchar",
						"length"        => 255,
					),
					"content" => array(
						"type"          => "longtext"
					),
				)
			);*/
	}
}