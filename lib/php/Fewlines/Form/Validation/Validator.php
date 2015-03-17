<?php

namespace Fewlines\Form\Validation;

abstract class Validator implements IValidator
{
	/**
	 * @var \Fewlines\Form\Validation\Result
	 */
	protected $result;
}