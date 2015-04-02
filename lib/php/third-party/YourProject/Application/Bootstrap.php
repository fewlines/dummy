<?php
namespace YourProject\Application;

class Bootstrap {
	/**
	 * @param \Fewlines\Application\Application $app
	 */
	public function __construct($app) {
		$app->setEnv('local');
	}
}