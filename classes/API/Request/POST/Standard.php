<?php defined('SYSPATH') or die('No direct script access.');

/**
 * load API request from POST of resource_data
 */
class API_Request_POST_Standard extends API_Request {
	/**
	 * @see parent::load_request()
	 */
	public function load_request()
	{
		parse_str(Request::current()->body(), $input);
		$this->resource_data = $input['resource_data'];
	}
}
