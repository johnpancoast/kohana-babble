<?php defined('SYSPATH') or die('No direct script access.');

/**
 * exception relating to media type encoding/decoding
 */
class Babble_API_MediaType_Exception_Encoding extends Exception {
	/** 
	 * @var int A code that _should_ correlate with an API code in config/base/api.php
	 */
	protected $proposed_response_code = NULL;

	/**
	 * constructor sets response code
	 * @param string $message The message
	 * @param string $proposed_response_code A proposed api response code. It _should_ match a code from config/base/api.php but that's up to the dev.
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
	public function get_code()
	{
		return $this->proposed_response_code;
	}
}
