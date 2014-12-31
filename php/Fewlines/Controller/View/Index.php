<?php
/**
 * fewlines CMS
 *
 * Description: The index controller
 *
 * @copyright Copyright (c) fewlines
 * @author Davide Perozzi
 */

namespace Fewlines\Controller\View;

use Fewlines\Session\Session;
use Fewlines\Crypt\Crypt;
use Fewlines\Helper\PathHelper;
use Fewlines\Database\Database;

class Index extends \Fewlines\Controller\Template
{
	public function indexAction()
	{
		/**
		 * Session handling
		 */

		//$session = new Session("testCookie", "test");
		$cook      = Session::get('testCookie');

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

		$db           = new Database;
		$resultInsert = $db->select("tabletest", array("content", "password"))->insert(array("cont", md5("pass" . rand(0,100))))/*->execute()*/;
		$resultUpdate = $db->select("tabletest", array("content", "password"))->update(array("updatedcontent", "updatedpassword"))->where(array("id", "=", 2), "OR")->where(array("id", "=", 3))/*->execute()*/;
		$resultDelete = $db->select("tabletest")->where(array("id", ">", 0))->delete()/*->execute()*/;
		$resultFetch  = $db->select("tabletest", "*")->where(array("id", ">", 1))->where(array("id", "<", 4))->limit(0,5)->fetchAll();

		pr($resultFetch);
	}
}

?>