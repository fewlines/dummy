<?php

namespace Fewlines\Controller;

interface ITemplate
{
	/**
	 * @param \Fewlines\Template\Template $template
	 */
	public function init(\Fewlines\Template\Template &$template);
}