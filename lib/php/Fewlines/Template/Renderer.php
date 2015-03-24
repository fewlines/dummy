<?php

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
	 * The result of the controller to display
	 * in the view
	 *
	 * @var string
	 */
	private $controller;

	/**
	 * Init function (called from child)
	 */
	public function initLayout()
	{
		// Set layout to handle with
		$this->layout = $this->getLayout();
	}

	public function initHashmap()
	{
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
		pr("render layout");

		// Update layout
		$this->initHashmap();
		$this->initLayout();

		// Call controller from view (if exists)
		$this->controller = $this->layout->initViewController();

		pr("renderer");
		pr($this->layout->getLayoutName());

		// Render layout
		echo $this->getRenderedHtml($this->layout->getLayoutPath());
	}

	/**
	 * Renders a view and returns the content
	 * A optional view if possible. If no view is
	 * given. The view of the layout will be
	 * taken.
	 *
	 * @param  string $view
	 */
	protected function renderView($view = '')
	{
		if(true == empty($view))
		{
			// Get view and action
			$file   = $this->layout->getViewPath();
			$action = $this->layout->getViewAction();

			if(is_string($this->controller))
			{
				// Output rendered html from the return of the controller
				echo $this->controller;
			}
			else
			{
				// Output rendered html (view)
				echo $this->getRenderedHtml($file);
			}
		}
		else
		{
			$file = PathHelper::getRealViewPath($view, '', $this->layout->getLayoutName());

			if(true == file_exists($file))
			{
				$file = $this->getRenderedHtml($file);
			}

			return $file;
		}
	}
}