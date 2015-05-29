<?php

/**
 * fewlines Framework
 *
 *  - Copyright:  fewlines
 *  - Developers: Davide Perozzi
 *
 *  - Framework inspired by Zend Framework (http://framework.zend.com/)
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
 * Instantiate the application
 *
 * -----------------------------------------
 *
 * To reactivate the installation,
 * please go to "ETC_DIR/cfg/fewlines/"
 * and uncomment the file "Install.xml".
 * Just rename it to "_Install.xml", so the
 * application will ignore it and pretend
 * to be a uninstalled version of fewlines
 */

(new \Fewlines\Application\Application)
	->bootstrap()
	->run();