<?php defined('SYSPATH') or die('No direct script access.');

/**
 * load API request from POST of xml data
 */
class API_Request_POST_XML extends API_Request {
	/**
	 * @see parent::load_request()
	 */
	public function load_request()
	{
		// currently unimplemented
		throw new API_Response_Exception('XML request loader unimplemented', '406-001');
	}
}
