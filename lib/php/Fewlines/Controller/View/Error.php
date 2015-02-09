<?php

namespace Fewlines\Controller\View;

class Error extends \Fewlines\Controller\Template
{
	/**
	 * Holds the current response code
	 *
	 * @var integer
	 */
	private $responseCode;

	public function indexAction()
	{
		$this->responseCode = $this->httpRequest->getStatusCode();
		$viewName = $this->template->getLayout()->getRealViewName();

		$this->assign('responseCode', $this->responseCode);
		$this->assign('errorMessage', $this->getMessage($viewName));
	}

	/**
	 * Returns the error description
	 * for the view
	 *
	 * @param  string $viewName
	 * @return string
	 */
	private function getMessage($viewName)
	{
		$message = "An error occured";

		switch($this->responseCode)
		{
			case 404:
				$message = "Page \"" . $viewName . "\" not found!";
			break;
		}

		return $message;
	}
}