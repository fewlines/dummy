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
		$crypter = new Crypt;

		/*$session = new Session("testCookie", "potassdtoe", 30303039338938, true);
		$cook = Session::get('testCookie');

		if($cook->isSession())
		{
			echo "SESS: " . $cook->getSession()->getContent() . "<br />";
		}

		if($cook->isCookie())
		{
			echo "COOK: " . $cook->getCookie()->getContent() . "<br />";
		}*/
	}
}

?>