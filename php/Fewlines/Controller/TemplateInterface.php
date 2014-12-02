<?php
/**
 * fewlines CMS
 *
 * Description: Interface for all
 * view controllers
 *
 * @copyright Copyright (c) fewlines
 * @author Davide Perozzi
 */
namespace Fewlines\Controller;

interface TemplateInterface
{
	/**
	 * @param \Fewlines\Template\Template $template
	 */
	public function init(\Fewlines\Template\Template $template);
}

?>