<?php

namespace Fewlines\Template;

class Caller extends Renderer
{
	/**
	 * The current layout
	 * received by the child template
	 *
	 * @var \Fewlines\Template\Layout
	 */
	private $layout;

	/**
	 * @param \Fewlines\Template\Layout $layout
	 */
	public function initCaller($layout)
	{
		$this->layout = $layout;
	}

	/**
	 * Handles all get requests
	 *
	 * @param  string $name
	 * @return *
	 */
	public function __get($name)
	{
		$controller = $this->layout->getController();

		if(false == is_null($controller) &&
			true == property_exists($controller, $name))
		{
			return $controller->{$name};
		}
		else if(false == property_exists($this, $name))
		{
			throw new Exception\PropertyNotFoundException(
				"Could not receive the property \"" . $name . "\".
				It does not exist."
			);
		}

		return $this->{$name};
	}

	/**
	 * Calls undefined functions
	 * (mostly used for view helpers)
	 *
	 * @param  string $name
	 * @param  array  $value
	 * @return *
	 */
	public function __call($name, $args)
	{
		if(true == preg_match($this->viewHelperExp, $name))
		{
			$helperName  = preg_replace($this->viewHelperExp, '', $name);
			$helperClass = 'Fewlines\Helper\View\\' . $helperName;

			if(false == class_exists($helperClass))
			{
				throw new Exception\HelperNotFoundException(
					"View helper \"" . $helperClass . "\"
					was not found!"
				);
			}

			$helper = $this->getHelperClass($helperClass);

			if(false == ($helper instanceof \Fewlines\Helper\AbstractViewHelper))
			{
			 	throw new Exception\HelperInvalidInstanceException(
			 		"The view helper \"" . $helperName . "\" was
			 		NOT extended by \Fewlines\Helper\AbstractViewHelper"
			 	);
			}

			if(false == method_exists($helper, $helperName))
			{
				throw new Exception\HelperMethodNotFoundException(
					"The view helper method \"" . $helperName . "\"
					was not found!"
				);
			}

			$reflection     = new \ReflectionMethod($helperClass, $helperName);
    		$needArgsCount  = $reflection->getNumberOfRequiredParameters();
    		$foundArgsCount = count($args);

    		if($needArgsCount > $foundArgsCount)
    		{
    			throw new Exception\HelperArgumentException(
    				"The view helper method \"" . $helperName ."\"
    				requires at least " . $needArgsCount . "
    				parameter(s). Found " . $foundArgsCount
    			);
    		}

    		return call_user_func_array(array($helper, $helperName), $args);
		}
		else
		{
			$controller = $this->layout->getController();

			if(false == is_null($controller) &&
				true == method_exists($controller, $name))
			{
				return call_user_func_array(array($controller, $name), $args);
			}
			else if(false == method_exists($this, $name))
			{
				$msg = "The method \"" . $name . "\" was not found in " . get_class($this);

				if(false == is_null($controller))
				{
					$msg .= " or in the controller . " . get_class($controller);
				}

				throw new Exception\TemplateMethodNotFoundException($msg);
			}

			return call_user_func_array(array($this, $name), $args);
		}
	}
}