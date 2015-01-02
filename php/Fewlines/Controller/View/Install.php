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
		/**
		 * @todo CHANGE view rendering behaviour add folder for each layout
		 * in the view folder
		 */
		 echo "<b>step1</b>";
	}
}

?>