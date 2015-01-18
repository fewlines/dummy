<?php

namespace Fewlines\Template;

class Caller extends Renderer
{
	/**
	 * Handles all get requests
	 *
	 * @param  string $name
	 * @return *
	 */
	public function __get($name)
	{
		if(!property_exists($this, $name))
		{
			throw new Exception\PropertyNotFoundException(
				"Could not receive the property \"" . $name . "\".
				It does not exist."
			);
		}

		return $this->$name;
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
		if(preg_match($this->viewHelperExp, $name))
		{
			$helperName = preg_replace($this->viewHelperExp, '', $name);
			$helperClass = 'Fewlines\Helper\View\\' . $helperName;

			if(!class_exists($helperClass))
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

			if(!method_exists($helper, $helperName))
			{
				throw new Exception\HelperMethodNotFoundException(
					"The view helper method \"" . $helperName . "\"
					was not found!"
				);
			}

			$reflection = new \ReflectionMethod($helperClass, $helperName);
    		$needArgsCount = $reflection->getNumberOfRequiredParameters();
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
			if(!method_exists($this, $name))
			{
				throw new Exception\TemplateMethodNotFoundException(
					"The method \"" . $name . "\" was not found in
					" . get_class($this)
				);
			}

			return call_user_func_array(array($this, $name), $args);
		}
	}
}