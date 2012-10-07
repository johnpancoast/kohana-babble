<?php defined('SYSPATH') or die('No direct script access.');

/**
 * generic api response exception
 */
class API_Model_Exception extends Exception {
	/** 
	 * @var int A code that _should_ correlate with an API code in config/base/api.php
	 */
	protected $proposed_response_code = NULL;

	/**
	 * constructor sets response code
	 * @param string $message The message
	 * @param string $proposed_response_code A proposed api response code. It _should_ match a code from config/base/api.php but that's up to the dev.
	 * @TODO add error logging of passed message and the response_code and associated private response message in config
	 * @TODO make response code just normal exception code, break passed codes into normal http code, then internal code as new param
	 * and change clients that use get_response_http_cdoe to just use standard getCode().
	 */
	public function __construct($message, $proposed_response_code = NULL)
	{
		parent::__construct($message);

		// set our response code
		$this->proposed_response_code = $proposed_response_code;
	}

	/**
	 * get response code
	 * @access public
	 * @return self::$response_code
	 */
	public function get_response_code()
	{
		return $this->proposed_response_code;
	}
}
