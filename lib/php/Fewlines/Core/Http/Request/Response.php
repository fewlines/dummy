<?php
namespace Fewlines\Core\Http\Request;

class Response
{
	/**
	 * Returns the status code
	 *
	 * @return integer
	 */
	public function getStatusCode() {
		return http_response_code();
	}
}