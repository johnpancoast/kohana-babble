<?php defined('SYSPATH') or die('No direct script access.');

/**
 * json api response
 */
class API_Response_Driver_Application_JSON extends API_Response {
	/**
	 * @see parent::get_response_encoded()
	 */
	public function get_response_encoded()
	{
		return json_encode($this->get_response());
	}
}
