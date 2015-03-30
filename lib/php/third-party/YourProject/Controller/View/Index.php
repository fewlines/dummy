<?php
namespace YourProject\Controller\View;

class Index extends \Fewlines\Controller\Template
{
	public function indexAction() {
		echo "Action called from: " . __CLASS__;

		// Assign version
		$this->assign('version', $this->getConfig()->getElementByPath('application/version'));
	}
}