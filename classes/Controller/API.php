<?php

/**
 * base api controller class that all api methods should eventually extend from
 */
class Controller_API extends Controller {
	/**
	 * @var API_Request An instance of an API_Request driver
	 */
	protected $api_request = null;

	/**
	 * called before method actual method call
	 * @access public
	 */
	public function before()
	{
		// TODO prob do some API security validation here.

		// must call parent before()
		parent::before();

		if ( ! $this->api_request)
		{
			#$this->api_request = API_Request::factory();
		}
	}
}
