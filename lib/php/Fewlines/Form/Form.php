<?php
namespace Fewlines\Form;

use Fewlines\Form\Element\Input;
use Fewlines\Form\Element\Select;
use Fewlines\Form\Element\Textarea;
use Fewlines\Helper\FunctionParseHelper;
use Fewlines\Dom\Dom as DomHelper;

class Form extends \Fewlines\Dom\Element
{
    /**
     * The element tagame of the config element
     * which contains all inputs etc.
     *
     * @var string
     */
    const XML_ELEMENTS_TAG = 'elements';

    /**
     * @var string
     */
    const SETTER_PREFIX = 'set';

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
     * Tells if autocomplete should be
     * allowed or not
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
     * @var \Fewlines\Dom\Dom
     */
    private $domHelper;

    /**
     * @var array
     */
    protected $attributes = array();

    /**
     * @var \Fewlines\Form\Validation
     */
    private $validation;

    /**
     * @var \Fewlines\Form\Result
     */
    private $result;

    /**
     * Init a form (with a given xml config)
     *
     * @param \Fewlines\Xml\Tree\Element|null $config
     */
    public function __construct(\Fewlines\Xml\Tree\Element $config = null) {
        // Set dom relevant flags
        $this->setDomStr(self::FORM_STR);
        $this->setDomTag(self::FORM_TAG);

        // Create result to store all validations
        $this->result = new Result;

        // Add config by xml
        if (true == ($config instanceof \Fewlines\Xml\Tree\Element)) {
            $this->config = $config;

            // Add own attributes (of the form element)
            $this->setFormAttributesByConfig();

            // Add global validation for all inputs (if exists)
            $validation = $this->config->getChildByName('validation', false);
            if (true == ($validation instanceof \Fewlines\Xml\Tree\Element)) {
                $this->validation = new Validation($validation->getChildByName('errors', false));
            }

            // Get form items defined in the xml config
            $elements = $this->config->getChildren();

            // Add the form elements from the config as element
            if (false != $elements && $elements > 0) {
                $this->addElementsByXmlConfig($elements);
            }
        }
    }

    /**
     * @param  array   $ctx
     * @param  boolean $mergeCtx
     * @return \Fewlines\Form\Form
     */
    public function validate($ctx = array(), $mergeCtx = false) {
        foreach ($this->elements as $element) {
            if ($element->hasValidation()) {
                $this->result->addError($element->getName(), $element->validate($this->getElementValue($element, $ctx, $mergeCtx))->getResult());
            }
        }

        return $this;
    }

    /**
     * @return \Fewlines\Form\Result
     */
    public function getResult() {
        return $this->result;
    }

    /**
     * @param  string $name
     * @return array|null|\Fewlines\Form\Element\Element
     */
    public function getElementsByName($name, $collect = true) {
        $result = array();

        foreach ($this->elements as $element) {
            if ($element->getName() == $name) {
                if (false == $collect) {
                    return $element;
                }

                $result[] = $element;
            }
        }

        if (true == $collect) {
            return $result;
        }
        else {
            return null;
        }
    }

    /**
     * @param  string $name
     * @return \Fewlines\Form\Element\Element|null
     */
    public function getElementByName($name) {
        return $this->getElementsByName($name, false);
    }

    /**
     * @param  \Fewlines\Form\Element\Element $name
     * @param  array   $ctx
     * @param  boolean $mergeCtx
     * @return string|array
     */
    private function getElementValue($element, $ctx = array(), $mergeCtx = false) {
        if (false == empty($ctx) && true == is_array($ctx) && false == $mergeCtx) {
            $content = $ctx;
        }
        else {
            if ($this->method == 'post') {
                $content = $_POST;
            }
            else {
                $content = $_GET;
            }
        }

        if (true == $mergeCtx) {
            $content = array_merge($content, $ctx);
        }

        $name = $element->getName();

        return array_key_exists($name, $content) ? $content[$name] : '';
    }

    /**
     * @return array
     */
    public function getData() {
        $data = array();

        foreach ($this->elements as $element) {
            $data[$element->getName()] = $this->getElementValue($element);
        }

        return $data;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
        $this->addAttribute('name', $name);
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $method
     */
    public function setMethod($method) {
        $method = strtolower($method);

        if ($method != 'post' && $method != 'get') {
            throw new Exception\MethodDoesNotExistException("
				The method \"" . $method . "\" does not exist.
				Use \"POST\" or \"GET\".
			");
        }

        $this->method = $method;
        $this->addAttribute('method', $method);
    }

    /**
     * @return string
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * @param string $target
     */
    public function setTarget($target) {
        $this->target = $target;
        $this->addAttribute('target', $target);
    }

    /**
     * @return string
     */
    public function getTarget() {
        return $this->target;
    }

    /**
     * @param string $charset
     */
    public function setAcceptCharset($charset) {
        $this->acceptCharset = $charset;
        $this->addAttribute('accept-charset', $acceptCharset);
    }

    /**
     * @return string
     */
    public function getAcceptCharset() {
        return $this->acceptCharset;
    }

    /**
     * @param boolean|string $noValidate
     */
    public function setNoValidate($noValidate) {
        $this->noValidate = filter_var($noValidate, FILTER_VALIDATE_BOOLEAN);
        $this->addAttribute('novalidate', $noValidate);
    }

    /**
     * @return boolean
     */
    public function isNovalidate() {
        return $this->noValidate;
    }

    /**
     * @param string $autoComplete
     */
    public function setAutoComplete($autoComplete) {
        $autoComplete = strtolower($autoComplete);

        if ($autoComplete != 'on' && $autoComplete != 'off') {
            throw new Exception\AutoCompleteValueInvalidException("
				Please define a valid trigger. ON or OFF.
			");
        }

        $this->autoComplete = $autoComplete;
        $this->addAttribute('autocomplete', $autoComplete);
    }

    /**
     * @param string $action
     */
    public function setAction($action) {
        $this->action = $action;
        $this->addAttribute('action', $action);
    }

    /**
     * @return string
     */
    public function getAction() {
        return $this->action;
    }

    /**
     * @param string $encType
     */
    public function setEncType($encType) {
        $encType = strtolower($encType);

        if ($encType != 'application/x-www-form-urlencoded' && $encType != 'multipart/form-data' && $encType != 'text/plain') {
            throw new Exception\InvalidEncTypeException("
				Please enter a valid enctype for the
				formular.
	   		");
        }

        $this->encType = $encType;
        $this->addAttribute('enctype', $encType);
    }

    /**
     * @return string
     */
    public function getEncType() {
        return $this->encType;
    }

    /**
     * @param string $name
     * @param string $content
     */
    public function addAttribute($name, $content) {
        $this->attributes[$name] = $content;
    }

    /**
     * @return array
     */
    public function getAttributes() {
        return $this->attributes;
    }

    /**
     * @param  string $name
     * @return string
     */
    public function getAttribute($name) {
        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }

        return '';
    }

    /**
     * @return array
     */
    public function getElements() {
        return $this->elements;
    }

    /**
     * Adds the attributes from the given config
     * to itself
     */
    private function setFormAttributesByConfig() {
        if ($this->config instanceof \Fewlines\Xml\Tree\Element) {
            foreach ($this->config->getAttributes() as $name => $content) {
                $content = FunctionParseHelper::parseLine($content);

                switch (strtolower($name)) {
                    case 'name':
                        $this->setName($content);
                        break;

                    case 'method':
                        $this->setMethod($content);
                        break;

                    case 'target':
                        $this->setTarget($content);
                        break;

                    case 'accept-charset':
                        $this->setAcceptCharset($content);
                        break;

                    case 'novalidate':
                        $this->setNoValidate($content);
                        break;

                    case 'autocomplete':
                        $this->setAutoComplete($content);
                        break;

                    case 'action':
                        $this->setAction($content);
                        break;

                    case 'enctype':
                        $this->setEncType($content);
                        break;

                    default:
                        $this->addAttribute($name, $content);
                        break;
                }
            }
        }
    }

    /**
     * Adds elements from a xml config
     * @param array $elements
     */
    private function addElementsByXmlConfig($elements) {
        foreach ($elements as $element) {
            $validation = $element->getChildByName('validation', false);

            switch (strtolower($element->getName())) {
                case Input::HTML_TAG:
                    $type = $element->getAttribute('type');
                    $inputName = $element->getAttribute('name');
                    $attributes = $element->getAttributes();

                    if (false == empty($type) && false == empty($inputName)) {
                        $this->addElement(Input::HTML_TAG, $inputName, $attributes, $validation);
                    }
                    break;

                case Select::HTML_TAG:
                    $inputName = $element->getAttribute('name');
                    $options = $element->getChildrenByName('option');
                    $attributes = $element->getAttributes();
                    $attributes['options'] = $options;

                    if (false == empty($inputName)) {
                        $this->addElement(Select::HTML_TAG, $inputName, $attributes, $validation);
                    }
                    break;

                case Textarea::HTML_TAG:
                    $inputName = $element->getAttribute('name');
                    $attributes = $element->getAttributes();
                    $attributes['content'] = (string)$element;

                    if (false == empty($inputName)) {
                        $this->addElement(Textarea::HTML_TAG, $inputName, $attributes, $validation);
                    }
                    break;
            }
        }
    }

    /**
     * Adds a form item to the
     * formular
     *
     * @param string $type
     * @param string $name
     * @param array  $attributes
     * @param array|\Fewlines\Xml\Tree\Element $validation
     * @return \Fewlines\Form\Form
     */
    public function addElement($type, $name, $attributes = array(), $validation = array()) {
        $element = null;

        // Force name tag to be set as attribute
        // for the dom element
        if (false == array_key_exists('name', $attributes)) {
            $attributes['name'] = $name;
        }

        // Create element by type
        switch (strtolower($type)) {
            case Input::HTML_TAG:
                if (false == array_key_exists('type', $attributes)) {
                    return;
                }

                $class = __NAMESPACE__ . "\\Element\\Input\\";
                $class.= ucfirst($attributes['type']);

                $element = new $class;
                break;

            case Select::HTML_TAG:
                $class = __NAMESPACE__ . "\\Element\\Select";
                $element = new $class;

                if (array_key_exists('options', $attributes) && is_array($attributes['options'])) {
                    $options = $attributes['options'];

                    for ($i = 0, $len = count($options); $i < $len; $i++) {
                        if ($options[$i] instanceof \Fewlines\Xml\Tree\Element) {
                            $content = (string)$options[$i];
                            $value = $options[$i]->getAttribute("value");
                            $selected = $options[$i]->getAttribute("selected");
                        }
                        else if (true == is_array($options[$i])) {
                            $content = $options[$i]['content'];
                            $value = $options[$i]['value'];
                            $selected = array_key_exists('selected', $options[$i]) ? $options[$i]['selected'] : '';
                        }
                        else {
                            throw new Exception\SelectOptionInvalidException("
								The option given has no valid format to
								convert it.
							");
                        }

                        if (empty($selected)) {
                            $selected = "false";
                        }

                        $element->addOption(Select::createOption($content, $value, $selected));
                    }

                    unset($attributes['options']);
                }
                break;

            case Textarea::HTML_TAG:
                $class = __NAMESPACE__ . "\\Element\\Textarea";
                $element = new $class;
                break;
        }

        if ($element instanceof \Fewlines\Form\Element) {
            // Set element name
            $element->setName($name);

            // Set other attributes
            $this->addElementAttributes($element, $attributes);

            // Set validation
            if ($validation != false) {
                if (true == ($validation instanceof \Fewlines\Xml\Tree\Element)) {
                    $element->setValidation($validation->getChildByName('errors'), $validation->getChildByName('options'), $this->getValidationErrors());
                }
                else if (true == is_array($validation)) {
                    if (true == array_key_exists('options', $validation) && true == array_key_exists('errors', $validation)) {
                        $element->setValidation($validation['errors'], $validation['options'], $this->getValidationErrors());
                    }
                    else {
                        throw new Exception\ValidationParametersEmptyException("Please set the keys errors and options for a valid
						validation of the given element. Use it in the
						last argument section as array.");
                    }
                }
            }
        }

        if (false == is_null($element)) {
            $this->elements[] = $element;
        }

        return $this;
    }

    /**
     * @param *     $element
     * @param array $attributes
     */
    public function addElementAttributes($element, $attributes) {
        foreach ($attributes as $name => $content) {
            $method = self::SETTER_PREFIX . ucfirst($name);

            if (true == method_exists($element, $method)) {
                $element->{$method}($content);
            }

            $element->addAttribute($name, $content);
        }
    }

    /**
     * Gets the validation errors
     *
     * @return array
     */
    public function getValidationErrors() {
        if (true == ($this->validation instanceof \Fewlines\Form\Validation)) {
            return $this->validation->getErrors();
        }

        return array();
    }
}
