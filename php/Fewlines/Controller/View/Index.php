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

class Index extends \Fewlines\Controller\Template
{
	public function indexAction()
	{
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
	}
}

?>