<?php

/**
 * base api controller class that all api methods should eventually extend from
 */
class Controller_API extends Controller {
	/**
	 * @var API_Request An instance of an API_Request driver
	 * @access protected
	 */
	protected $api_request = null;

	/**
	 * @var API_Response An instance of an API_Response driver
	 * @access protected
	 */
	protected $api_response = null;

	/**
	 * called before method actual method call
	 * @access public
	 * @uses API_Request
	 * @uses API_Response
	 */
	public function before()
	{
		// TODO prob do some API security validation here.

		// must call parent before()
		parent::before();

		$this->api_request = API_Request::factory();
		$this->api_response = API_Response::factory();
	}
}
