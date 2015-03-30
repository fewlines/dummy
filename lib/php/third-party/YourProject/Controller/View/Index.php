<?php
namespace YourProject\Controller\View;

class Index extends \Fewlines\Controller\Template
{
	public function indexAction() {
		echo "Action called from: " . __CLASS__;
	}
}