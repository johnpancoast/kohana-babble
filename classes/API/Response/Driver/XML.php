<?php defined('SYSPATH') or die('No direct script access.');

/**
 * xml api response
 */
class API_Response_Driver_XML extends API_Response {
	/**
	 * @see parent::get_encoded_response()
	 */
	public function get_encoded_response()
	{
		// unimplemented
		throw new API_Response_Exception('XML response driver unimplemented', '406-002');
	}
}
