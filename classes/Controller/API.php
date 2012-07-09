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
			$method = Request::current()->param('method');
			$request_driver = strtolower($method ? $method : 'json');
			switch ($request_driver)
			{
				case 'json':
					$request_driver = 'JSON';
					break;
				case 'xml':
					$request_driver = 'XML';
					break;
				case 'nv':
					$request_driver = 'NameValue';
					break;
			}
			$this->api_request = API_Request::factory($request_driver);
		}
	}
}
