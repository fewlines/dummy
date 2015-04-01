<?php
namespace YourProject\Controller\View;

class Index extends \Fewlines\Controller\View
{
	public function indexAction() {
		echo "Action called from: " . __CLASS__;

		// Assign version
		$this->assign('version', $this->getConfig()->getElementByPath('application/version'));
	}

	public function hueAction(){
		return 'huteast';
	}

	public function testrouteAction() {
		return 'hue he hue h7e hue hu e';
	}
}