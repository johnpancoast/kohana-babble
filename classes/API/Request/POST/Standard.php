<?php defined('SYSPATH') or die('No direct script access.');

/**
 * load API request from $_POST of resource_data
 */
class API_Request_POST_Standard extends API_Request {
	/**
	 * @see parent::load_request()
	 */
	public function load_request()
	{
		$this->request_resource_data = Request::current()->post('resource_data');
	}
}
