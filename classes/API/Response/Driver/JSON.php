<?php defined('SYSPATH') or die('No direct script access.');

/**
 * json api response
 */
class API_Response_Driver_JSON extends API_Response {
	/**
	 * @see parent::get_encoded_response()
	 */
	public function get_encoded_response()
	{
		return json_encode($this->response);
	}
}
