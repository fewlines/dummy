<?php

/**
 * fewlines Framework
 *
 *  - Copyright:  fewlines
 *  - Developers: Davide Perozzi
 *
 *  - Inspired by Zend Framework (http://framework.zend.com/)
 *
 * -------------------------------------
 *
 * Note: We are searching for a (german) developer!
 * Feel free to contact us.
 *
 * Skills: PHP, JavaScript (Google closure), HTML, CSS, ...
 * ... and all skills a web developer needs.
 *
 * -------------------------------------
 *
 * If you find any bugs feel free to contact
 * a developer. Or became a developer and help
 * to improve fewlines ;)
 */

require_once __DIR__ . '/fl_init.php';

/**
 * Bootstrap application and render the
 * frontend with the given parameters
 */

(new \Fewlines\Core\Application\Application)
	->bootstrap()
	->run();