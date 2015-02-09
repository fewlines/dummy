<?php

namespace Fewlines\Controller;

interface TemplateInterface
{
	/**
	 * @param \Fewlines\Template\Template $template
	 */
	public function init(\Fewlines\Template\Template $template);
}