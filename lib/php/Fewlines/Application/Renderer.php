<?php
namespace Fewlines\Application;

use Fewlines\Template\Template;

abstract class Renderer
{
	/**
	 * Renders a template
	 *
	 * @param array $args
	 */
	final protected static function renderTemplate($layout, $args = array()) {
		Buffer::start();
		Template::getInstance()->setLayout($layout)->renderAll($args);
	}

	/**
	 * Renders a exception/error template
	 *
	 * @param array $args
	 */
	final protected static function renderException($args) {
		Buffer::clear(true);
		self::renderTemplate(EXCEPTION_LAYOUT, $args);
		exit;
	}
};