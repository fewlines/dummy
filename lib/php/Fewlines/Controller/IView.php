<?php

namespace Fewlines\Controller;

interface IView
{
	/**
	 * @param \Fewlines\Template\Template $template
	 */
	public function init(\Fewlines\Template\Template &$template);
}