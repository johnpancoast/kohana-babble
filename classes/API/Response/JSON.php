<?php defined('SYSPATH') or die('No direct script access.');

/**
 * json api response
 */
class API_Response_JSON extends API_Response {
	public function get_encoded_response()
	{
		return json_encode($this->response);
	}
}
