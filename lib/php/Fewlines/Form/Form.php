<?php 

namespace Fewlines\Form;

class Form
{
	/**
	 * The element taname of the config element
	 * which contains all inputs etc.
	 * 
	 * @var string
	 */
	const XML_ELEMENTS_TAG = 'elements';

	/**
	 * The config tree 
	 * 
	 * @var \Fewlines\Xml\Tree\Element|null
	 */
	private $config = null;

	/**
	 * Identifer for the form
	 * 
	 * @var string
	 */
	private $name = '';

	/**
	 * The method of the form
	 * 
	 * @var string
	 */
	private $method = 'post';

	/**
	 * The type to open the action
	 * 
	 * @var string 
	 */
	private $target = '_self';

	/**
	 * Charset used in the submitted form
	 * 
	 * @var string
	 */
	private $acceptCharset = 'UTF-8';

	/**
	 * Tells if the browser will check the form after
	 * a submit
	 * 
	 * @var boolean
	 */
	private $noValidate = false;

	/**
	 * Telss if autocomplete should be 
	 * allowd or not
	 * 
	 * @var string
	 */
	private $autoComplete = 'on';

	/**
	 * The desitnation file after a submit
	 * 
	 * @var string
	 */
	private $action = '';

	/**
	 * How the data will be encoded after 
	 * submitting it
	 *
	 * Types:
	 * 	- application/x-www-form-urlencoded
	 *  - multipart/form-data
	 *  - text/plain
	 * 
	 * @var string
	 */
	private $encType = 'application/x-www-form-urlencoded';

	/**
	 * The elements in the formular
	 * 
	 * @var array
	 */
	private $elements = array();

	/**
	 * Init a form (with a given xml config)
	 * 
	 * @param \Fewlines\Xml\Tree\Element|null $config
	 */
	public function __construct(\Fewlines\Xml\Tree\Element $config = null)
	{
		if(true == $config instanceof \Fewlines\Xml\Tree\Element)
		{
			$this->config = $config;

			// Get form items defined in the xml config
			$elements = $this->config->getChildByName(self::XML_ELEMENTS_TAG);

			if(false != $elements && $elements->countChildren() > 0)
			{
				$children = $elements->getChildren();
				
				foreach($children as $element)
				{
					pr($element);
				}
			}
		}
	}

	/**
	 * Adds a form item to the 
	 * formular
	 * 
	 * @param string $element    
	 * @param string $name       
	 * @param array  $attributes 
	 */
	public function addElement($element, $name, $attributes = array())
	{

	}
}