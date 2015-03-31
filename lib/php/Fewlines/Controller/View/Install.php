<?php

namespace Fewlines\Controller\View;

use Fewlines\Http\Header as HttpHeader;
use Fewlines\Locale\Locale;

class Install extends \Fewlines\Controller\Template
{
	public function indexAction()
	{
		$this->redirect($this->getBaseUrl(array(
				"install", "step1"
			)));
	}

	public function step1Action()
	{

	}
}