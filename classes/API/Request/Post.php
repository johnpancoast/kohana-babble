<?php defined('SYSPATH') or die('No direct script access.');

class API_Request_Post extends API_Request {
	public function load_request()
	{
		$this->request_id = Request::current()->param('api_id');
		$this->request_post = Request::current()->post('api_data');
	}
}
