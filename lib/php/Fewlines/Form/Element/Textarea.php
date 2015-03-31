<?php

namespace Fewlines\Form\Element;

class Textarea extends \Fewlines\Form\Element
{
	/**
	 * @var string
	 */
	const HTML_TAG = 'textarea';

	/**
	 * @var string
	 */
	protected $content = '';

	/**
	 * @var integer
	 */
	protected $rows;

	/**
	 * @var integer
	 */
	protected $cols;

	/**
	 * @var string
	 */
	protected $placeholder;

	/**
	 * @var string
	 */
	protected $form;

	/**
	 * @var integer
	 */
	protected $maxlength;

	/**
	 * The wrap style of the content after a submit:
	 * 	- hard
	 *  - soft
	 *
	 * @var string
	 */
	protected $wrap;

	/**
	 * @var boolean
	 */
	protected $autofocus;

	/**
	 * Define dom element type
	 */
	public function __construct()
	{
		$this->setDomTag(self::TEXTAREA_TAG);
		$this->setDomStr(self::TEXTAREA_STR);
	}

	/**
	 * @param integer|string $rows
	 */
	public function setRows($rows)
	{
		$this->rows = (int) $rows;
	}

	/**
	 * @return integer
	 */
	public function getRows()
	{
		return $this->rows;
	}

	/**
	 * @param integer|string $rows
	 */
	public function setCols($cols)
	{
		$this->cols = (int) $cols;
	}

	/**
	 * @return integer
	 */
	public function getCols()
	{
		return $this->cols;
	}

	/**
	 * @param string $placeholder
	 */
	public function setPlaceholder($placeholder)
	{
		$this->placeholder = $placeholder;
	}

	/**
	 * @return string
	 */
	public function getPlaceholder()
	{
		return $this->placeholder;
	}

	/**
	 * @param string $form
	 */
	public function setForm($form)
	{
		$this->form = $form;
	}

	/**
	 * @return string
	 */
	public function getForm()
	{
		return $this->form;
	}

	/**
	 * @param integer|string $maxlength
	 */
	public function setMaxlength($maxlength)
	{
		$this->maxlength = $maxlength;
	}

	/**
	 * @return integer
	 */
	public function getMaxlength()
	{
		return $this->maxlength;
	}

	/**
	 * @param string $wrap
	 */
	public function setWrap($wrap)
	{
		$this->wrap = $wrap;
	}

	/**
	 * @return string
	 */
	public function getWrap()
	{
		return $this->wrap;
	}

	/**
	 * @param string|boolean $autofocus
	 */
	public function setAutofocus($autofocus)
	{
		$this->autofocus = filter_var($autofocus, FILTER_VALIDATE_BOOLEAN);
	}

	/**
	 * @return string
	 */
	public function getAutofocus()
	{
		return $this->autofocus;
	}

	/**
	 * @param string $content
	 */
	public function setContent($content)
	{
		$this->content = $content;
	}

	/**
	 * @return string
	 */
	public function getContent()
	{
		return $this->content;
	}
}