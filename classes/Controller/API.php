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
	 * called before actual method call
	 * @access public
	 * @uses API_Request
	 * @uses API_Response
	 */
	public function before()
	{
		$this->api_request = API_Request::factory();
		$this->api_response = API_Response::factory();

		// if user not authentic, see if we've been passed an Authorization
		// header and attempt to log em in with that.
		if ( ! Auth::instance()->logged_in())
		{
			// get the user/key from auth header
			if ( ! isset($this->api_request->request_header['Authorization']))
			{
				throw new API_Response_Exception('unauthorized user', '401-000');
			}
			list($user, $key) = explode(':', $this->api_request->request_header['Authorization']);

			// get the api user from db.
			// make sure it's an API user, not normal user.
			$user = ORM::factory('user')
				->where('username', '=', $user)
				->where('api_user', '=', 1)
				->find();
			if ( ! $user->loaded())
			{
				throw new API_Response_Exception('unauthorized user', '401-000');
			}

			// gen a new hash from passed data and private key and check against passed hash.
			// if hashes match then the user has authenticated and we can log them in.
			$url = URL::base(Request::initial()).Request::initial()->uri().'?'.http_build_query(Request::initial()->query());
			$check_key = API_Request::get_auth_key($user->username, $user->password, $url, $_SERVER['REQUEST_METHOD'], array('resource_data' => $this->api_request->request_resource_data));
			if ( ! empty($key) && $key == $check_key)
			{
				Auth::instance()->force_login($user->username);
			}
			else
			{
				throw new API_Response_Exception('unauthorized user', '401-000');
			}
		}

		// check access list perms.
		// note that we have to call check_access() before the parent::before() call.
		// this is because if parent::before()'s call to check_access() fails it  will go to
		// 404 page (which doesn't work for API)
		if ( ! $this->check_access())
		{
			throw new API_Response_Exception('unauthorized access', '401-001');
		}

		// must call parent before()
		parent::before();
	}


	/**
	 * We override Controller::execute() so that we can handle API
	 * specifics.
	 * @see Kohana_Controller::execute()
	 * @return string Response
	 */
	public function execute()
	{
		try
		{
			// Execute the "before action" method
			// !!IMPOTANT AUTHENTICATION HAPPENS IN THIS METHOD!!
			$this->before();

			// Determine the action to use
			$action = 'action_'.strtolower($_SERVER['REQUEST_METHOD']);

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
			$this->response->body($this->api_response->set_response($e->get_response_code())->get_encoded_response());
		}
		catch (Kohana_HTTP_Exception $e)
		{
			$message = $e->getMessage();
			if (preg_match('/The requested URL (.*) was not found on this server./', $message))
			{
				$this->api_response->set_response('404-000');
			}
		}
		// if we received a generic error at this point, just throw/catch an API_Response_Exception.
		// we do this so that the normal API_Response_Exception
		// logging and api message handling can occur.
		catch (Exception $e)
		{
			try
			{
				throw new API_Response_Exception('(almost) uncaught API exception ('.$e->getMessage().')', '500-000');
			}
			catch (API_Response_Exception $e)
			{
				$this->api_response->set_response($e->get_response_code());
			}
		}

		// check that a response got set.
		// if we got no response at this point, just throw/catch an API_Response_Exception.
		// we do this so that the normal API_Response_Exception
		// logging and api message handling can occur.
		$response = $this->api_response->get_response();
		if ( ! $response || ! isset($response['code']))
		{
			try
			{
				throw new API_Response_Exception('no api response', '500-000');
			}
			catch (API_Response_Exception $e)
			{
				$this->api_response->set_response($e->get_response_code());
			}
		}

		// set http status code from response code
		$this->response->status(substr($response['code'], 0, 3));

		// send out main response from encoded api response
		$this->response->body($this->api_response->get_encoded_response());
		return $this->response;
	}

}
