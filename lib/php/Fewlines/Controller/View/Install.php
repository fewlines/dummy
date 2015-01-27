<?php
/**
 * fewlines CMS
 *
 * Description: The install controller
 *
 * @copyright Copyright (c) fewlines
 * @author Davide Perozzi
 */

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
		$install = Locale::get('install');

		pr($install);
	}
}