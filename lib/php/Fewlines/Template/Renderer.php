<?php
/**
 * fewlines CMS
 *
 * Description: A simple html renderer for the template
 * using new buffer
 *
 * Note: Should only be extended by \Fewlines\Template\Template
 *
 * @copyright Copyright (c) fewlines
 * @author Davide Perozzi
 */

namespace Fewlines\Template;

use Fewlines\Http\Header as HttpHeader;
use Fewlines\Helper\PathHelper;
use Fewlines\Helper\ArrayHelper;

class Renderer
{
	/**
	 * A array of all md5 hashes to save
	 * performmance
	 *
	 * @var array
	 */
	public static $md5VarHashmap = array(
		'file', 'config', 'varname', 'content', 'html'
	);

	/**
	 * Holds the current layout instance
	 *
	 * @var \Fewlines\Template\Layout
	 */
	private $layout;

	/**
	 * Init function (called from child)
	 */
	public function initLayout()
	{
		// Set layout to handle with
		$this->layout = $this->getLayout();

		// Calculate hashmaps (if they weren't just calculated)
		if(false == ArrayHelper::isAssociative(self::$md5VarHashmap))
		{
			for($i = 0; $i < count(self::$md5VarHashmap); $i++)
			{
				$name = self::$md5VarHashmap[$i];
				self::$md5VarHashmap[$name] = md5($name);

				unset(self::$md5VarHashmap[$i]);
			}
		}
	}

	/**
	 * Includes a php file and returns the content
	 * output with a buffer.
	 *
	 * Using md5 hashed variables to avoid override of
	 * the config variables from the user. Looks weird
	 * but to save performance the md5 hashes will only
	 * be calculated once
	 *
	 * @param  string $file
	 * @param  array  $config
	 * @return string
	 */
	protected function getRenderedHtml($file, $config = array())
	{
		ob_start();

		// Cache old vars
		${self::$md5VarHashmap['file']}   = $file;
		${self::$md5VarHashmap['config']} = $config;

		// Delete old variables
		unset($file, $config);

		// Define config variables
		foreach(
			${self::$md5VarHashmap['config']}  as
			${self::$md5VarHashmap['varname']} =>
			${self::$md5VarHashmap['content']}
		){
			${self::$md5VarHashmap['varname']} = (string) ${self::$md5VarHashmap['varname']};
			${${self::$md5VarHashmap['varname']}} = ${self::$md5VarHashmap['content']};
		}

		// Include the cached file
		include ${self::$md5VarHashmap['file']};

		// Get the output of the buffer form the included file
		${self::$md5VarHashmap['html']} = ob_get_contents();

		ob_clean();
		ob_end_flush();

		// Return the saved buffer
		return ${self::$md5VarHashmap['html']};
	}

	protected function renderLayout()
	{
		// Update layout
		$this->initLayout();

		$file = $this->layout->getLayoutPath();
		echo $this->getRenderedHtml($file);
	}

	protected function renderView()
	{
		// Get view and action from request
		$view   = $this->getRouteUrlPart('view');
		$action = $this->getRouteUrlPart('action');

		// Get view and action
		$file   = $this->layout->getViewPath();
		$action = $this->layout->getViewAction();

		// Call controller from view (if exists)
		$this->layout->initViewController();

		// Output rendered html
		echo $this->getRenderedHtml($file);
	}
}

?>