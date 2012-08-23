<?php defined('SYSPATH') or die('No direct script access.');

/**
 * load API request from $_REQUEST data
 */
class API_Request_Post extends API_Request {
	/**
	 * @see parent::load_request()
	 */
	public function load_request()
	{
		$this->request_resource_data = Request::current()->post('api_data');
	}
}
