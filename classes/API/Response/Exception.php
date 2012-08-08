<?php defined('SYSPATH') or die('No direct script access.');

/**
 * generic api response exception
 */
class API_Response_Exception extends Exception {
	/** 
	 * @var int A code that should correlate with an API code in config/base/api.php
	 */
	protected $response_code = null;

	/**
	 * constructor sets response code
	 * @TODO add error logging of passed message and the response_code and associated private response message in config
	 */
	public function __construct($message, $response_code)
	{
		parent::__construct($message);

		// set our response code
		$this->response_code = $response_code;
	}

	/**
	 * get response code
	 * @access public
	 * @return self::$response_code
	 */
	public function get_response_code()
	{
		return $this->response_code;
	}
}
