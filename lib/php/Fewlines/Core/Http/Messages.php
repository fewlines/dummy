<?php
namespace Fewlines\Core\Http;

trait Messages
{
	/**
	 * @var array
	 */
	private static $messages = array(
		/**
		 * Information
		 */

		100 => array(
			'status'  => 'Continue',
			'message' => ''
		),
		101 => array(
			'status'  => 'Switching Protocols',
			'message' => ''
		),
		102 => array(
			'status'  => 'Processing',
			'message' => ''
		),

		/**
		 * Successfully operations
		 */

		200 => array(
			'status'  => 'OK',
			'message' => ''
		),
		201 => array(
			'status'  => 'Created',
			'message' => ''
		),
		202 => array(
			'status'  => 'Accepted',
			'message' => ''
		),
		203 => array(
			'status'  => 'Non-Authoritative Information',
			'message' => ''
		),
		204 => array(
			'status'  => 'No Content',
			'message' => ''
		),
		205 => array(
			'status'  => 'Reset Content',
			'message' => ''
		),
		206 => array(
			'status'  => 'Partial Content',
			'message' => ''
		),
		207 => array(
			'status'  => 'Multi-Status',
			'message' => ''
		),
		208 => array(
			'status'  => 'Already Reported',
			'message' => ''
		),
		226 => array(
			'status'  => 'IM Used',
			'message' => ''
		),

		/**
		 * Redirect
		 */

		300 => array(
			'status'  => 'Multiple Choices',
			'message' => ''
		),
		301 => array(
			'status'  => 'Moved Permanently',
			'message' => ''
		),
		302 => array(
			'status'  => 'Found',
			'message' => ''
		),
		303 => array(
			'status'  => 'See Other',
			'message' => ''
		),
		304 => array(
			'status'  => 'Not Modified',
			'message' => ''
		),
		305 => array(
			'status'  => 'Use Proxy',
			'message' => ''
		),
		// 306 => array(
		// 	'status'  => '',
		// 	'message' => ''
		// ),
		307 => array(
			'status'  => 'Temporary Redirect',
			'message' => ''
		),
		308 => array(
			'status'  => 'Permanent Redirect',
			'message' => ''
		),

		/**
		 * Client errors
		 */

		400 => array(
			'status'  => 'Bad Request',
			'message' => ''
		),
		401 => array(
			'status'  => 'Unauthorized',
			'message' => ''
		),
		402 => array(
			'status'  => 'Payment Required',
			'message' => ''
		),
		403 => array(
			'status'  => 'Forbidden',
			'message' => ''
		),
		404 => array(
			'status'  => '404 Not Found',
			'message' => 'The page couldn\'t be found'
		),
		405 => array(
			'status'  => 'Method Not Allowed',
			'message' => ''
		),
		406 => array(
			'status'  => 'Not Acceptable',
			'message' => ''
		),
		407 => array(
			'status'  => 'Proxy Authentication Required',
			'message' => ''
		),
		408 => array(
			'status'  => 'Request Time-out',
			'message' => ''
		),
		409 => array(
			'status'  => 'Conflict',
			'message' => ''
		),
		410 => array(
			'status'  => 'Gone',
			'message' => ''
		),
		411 => array(
			'status'  => 'Length Required',
			'message' => ''
		),
		412 => array(
			'status'  => 'Precondition Failed',
			'message' => ''
		),
		413 => array(
			'status'  => 'Request Entity Too Large',
			'message' => ''
		),
		414 => array(
			'status'  => 'Request-URL Too Long',
			'message' => ''
		),
		415 => array(
			'status'  => 'Unsupported Media Type',
			'message' => ''
		),
		416 => array(
			'status'  => 'Requested range not satisfiable',
			'message' => ''
		),
		417 => array(
			'status'  => 'Expectation Failed',
			'message' => ''
		),
		418 => array(
			'status'  => 'I\'m a teapot',
			'message' => ''
		),
		420 => array(
			'status'  => 'Policy Not Fulfilled',
			'message' => ''
		),
		421 => array(
			'status'  => 'There are too many connections from your internet address',
			'message' => ''
		),
		422 => array(
			'status'  => 'Unprocessable Entity',
			'message' => ''
		),
		423 => array(
			'status'  => 'Locked',
			'message' => ''
		),
		424 => array(
			'status'  => 'Failed Dependency',
			'message' => ''
		),
		425 => array(
			'status'  => 'Unordered Collection',
			'message' => ''
		),
		426 => array(
			'status'  => 'Upgrade Required',
			'message' => ''
		),
		428 => array(
			'status'  => 'Precondition Required',
			'message' => ''
		),
		429 => array(
			'status'  => 'Too Many Requests',
			'message' => ''
		),
		431 => array(
			'status'  => 'Request Header Fields Too Large',
			'message' => ''
		),
		444 => array(
			'status'  => 'No Response',
			'message' => ''
		),
		449 => array(
			'status'  => 'The request should be retried after doing the appropriate action',
			'message' => ''
		),
		451 => array(
			'status'  => 'Unavailable For Legal Reasons',
			'message' => ''
		),

		/**
		 * Server errors
		 */

		500 => array(
			'status'  => '500 Internal Server Error',
			'message' => 'Something went wrong'
		),
		501 => array(
			'status'  => 'Not Implemented',
			'message' => ''
		),
		502 => array(
			'status'  => 'Bad Gateway',
			'message' => ''
		),
		503 => array(
			'status'  => 'Service Unavailable',
			'message' => ''
		),
		504 => array(
			'status'  => 'Gateway Time-out',
			'message' => ''
		),
		505 => array(
			'status'  => 'HTTP Version not supported',
			'message' => ''
		),
		506 => array(
			'status'  => 'Variant Also Negotiates',
			'message' => ''
		),
		507 => array(
			'status'  => 'Insufficient Storage',
			'message' => ''
		),
		508 => array(
			'status'  => 'Loop Detected',
			'message' => ''
		),
		509 => array(
			'status'  => 'Bandwidth Limit Exceeded',
			'message' => ''
		),
		510 => array(
			'status'  => 'Not Extended',
			'message' => ''
		)
 	);
}