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
				throw new API_Response_Exception('unauthorized user', '-9002');
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
				throw new API_Response_Exception('unauthorized user', '-9002');
			}

			// gen a new hash from passed data and private key and check against passed hash.
			// if hashes match then the user has authenticated and we can log them in.
			$protocol = (! empty($_SERVER['HTTPS']) ? 'https' : 'http');
			$url = Request::current()->url($protocol);
			$check_key = API_Request::get_auth_key($user->username, $user->password, $url, $_SERVER['REQUEST_METHOD'], array('api_data' => $this->api_request->request_post));
			if ( ! empty($key) && $key == $check_key)
			{
				Auth::instance()->force_login($user->username);
			}
			else
			{
				throw new API_Response_Exception('unauthorized user', '-9002');
			}
		}

		// check access list perms.
		// note that we have to call check_access() before the parent Controller class does.
		// this is because parent::before() will go to 404 page (which doesn't work for API)
		if ( ! $this->check_access())
		{
			throw new API_Response_Exception('unauthorized access', '-9002');
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
				$this->response->body($this->api_response->set_response('-9003')->get_encoded_response());
			}
		}
		// if we received a generic error at this point, just throw/catch an API_Response_Exception.
		// we do this so that the normal API_Response_Exception
		// logging and api message handling can occur.
		catch (Exception $e)
		{
			try
			{
				throw new API_Response_Exception('(almost) uncaught API exception ('.$e->getMessage().')', '-9000');
			}
			catch (API_Response_Exception $e)
			{
				$this->response->body($this->api_response->set_response($e->get_response_code())->get_encoded_response());
			}
		}

		// Return the response
		return $this->response;
	}

}
