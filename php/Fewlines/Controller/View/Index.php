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

class Index extends \Fewlines\Controller\Template
{
	public function indexAction()
	{
		//$session = new Session("testCookie", "potassdtoe", 120);
		$test = Session::get('testCookie');
	}
}

?>