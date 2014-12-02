<?php
/**
 * fewlines CMS
 *
 * Description: Handle all erroes (404, 500..)
 *
 * @copyright Copyright (c) fewlines
 * @author Davide Perozzi
 */

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

		$this->assign('responseCode', $this->responseCode);
		$this->assign('errorMessage', $this->getMessage());
	}

	/**
	 * Returns the error description
	 * for the view
	 *
	 * @return string
	 */
	private function getMessage()
	{
		$message = "An error occured";

		switch($this->responseCode)
		{
			case 404:
				$message = "Page not found!";
			break;
		}

		return $message;
	}
}

?>