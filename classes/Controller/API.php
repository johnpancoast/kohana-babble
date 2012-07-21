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
	 * ACL
	 */
	protected $access = array(
		// add, delete, edit require write
		'action_add' => array('user-write'),
		'action_delete' => array('user-write'),
		'action_edit' => array('user-write'),

		// all other methods require at least read
		'*' => array('user-read'),
	);

	/**
	 * called before method actual method call
	 * @access public
	 * @uses API_Request
	 * @uses API_Response
	 */
	public function before()
	{
		$this->api_request = API_Request::factory();
		$this->api_response = API_Response::factory();

		// note that we have to call check_access() before the parent Controller class does.
		// this is because parent::before() will go to 404 page (which doesn't work for API)
		if ( ! $this->check_access())
		{
			throw new API_Response_Exception('unauthorized', '-9002');
		}

		// must call parent before()
		parent::before();
	}


	/**
	 * We override Controller::execute() so that we can catch API exceptions.
	 * @see Kohana_Controller::execute()
	 * @return string Response
	 */
	public function execute()
	{
		try
		{
			// Execute the "before action" method
			$this->before();

			// Determine the action to use
			$action = 'action_'.$this->request->action();

			// If the action doesn't exist, it's a 404
			if ( ! method_exists($this, $action))
			{
				throw HTTP_Exception::factory(404,
					'The requested URL :uri was not found on this server.',
					array(':uri' => $this->request->uri())
				)->request($this->request);
			}

			// Execute the action itself
			$this->{$action}();

			// Execute the "after action" method
			$this->after();
		}
		catch (API_Response_Exception $e)
		{
			$this->response->body($this->api_response->set_response($e->getResponseCode())->get_encoded_response());
		}
		// if we received a generic error at this point, just throw/catch an API_Response_Exception.
		// we do this so that the normal API_Response_Exception
		// logging and api message handling can occur.
		catch (Exception $e)
		{
			try
			{
				throw new API_Response_Exception('something aint right with API ('.$e->getMessage().')', '-9000');
			}
			catch (API_Response_Exception $e)
			{
				$this->response->body($this->api_response->set_response($e->getResponseCode())->get_encoded_response());
			}
		}

		// Return the response
		return $this->response;
	}

}
