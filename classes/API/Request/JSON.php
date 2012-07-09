<?php defined('SYSPATH') or die('No direct script access.');

/**
 * json api request
 */
class API_Request_JSON extends API_Request {
	public function get_encoded_response($response)
	{
		return json_encode($response);
	}
}
