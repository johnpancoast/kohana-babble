<?php defined('SYSPATH') or die('No direct script access.');

/**
 * name value pair api response
 */
class API_Response_Driver_NameValue extends API_Response {
	/**
	 * @see parent::get_encoded_response()
	 */
	public function get_encoded_response()
	{
		// unimplemented
		throw new API_Response_Exception('NameValue response driver unimplemented', '406-002');
	}
}
