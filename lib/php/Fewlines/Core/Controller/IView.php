<?php

namespace Fewlines\Core\Controller;

interface IView
{
	/**
	 * @param \Fewlines\Core\Template\Template $template
	 */
	public function init(\Fewlines\Core\Template\Template &$template);
}