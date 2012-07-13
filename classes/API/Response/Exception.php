<?php defined('SYSPATH') or die('No direct script access.');

class API_Response_Exception extends Exception {
	protected $response_code = null;
	public function __construct($message, $response_code)
	{
		parent::__construct($message);

		// TODO add error logging of passed message and the response_code and associated private response message message
		// in config.

		// set our response code
		$this->response_code = $response_code;
	}

	public function getResponseCode()
	{
		return $this->response_code;
	}
}
