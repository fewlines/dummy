<?php
namespace YourProject\Controller\View;

use Fewlines\Component\Form\Form;

class Index extends \Fewlines\Core\Controller\View
{
	public function indexAction() {
		$form = new Form($this->getConfig()->getElementByPath('form/test'));

		if (!empty($_POST)) {
			$result = $form->validate()->getResult();
			pr($result);
		}

		$this->assign('form', $form);
	}

	public function hueAction(){
		return 'huteast';
	}

	public function testrouteAction() {
		return 'hue he hue h7e hue hu e';
	}
}