<?php
namespace Fewlines\Core\Controller\View;

class Index extends \Fewlines\Core\Controller\View
{
    public function indexAction() {
		$this->assign('version', $this->getConfig()->getElementByPath('application/version'));
    }
}
