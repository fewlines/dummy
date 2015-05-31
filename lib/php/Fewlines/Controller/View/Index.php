<?php
namespace Fewlines\Controller\View;

use Fewlines\Session\Session;
use Fewlines\Crypt\Crypt;
use Fewlines\Helper\PathHelper;
use Fewlines\Database\Database;
use Fewlines\Form\Form;
use Fewlines\Locale\Locale;
use Fewlines\Template\Template;

class Index extends \Fewlines\Controller\View
{
    public function indexAction() {
		$this->assign('version', $this->getConfig()->getElementByPath('application/version'));
    }
}
