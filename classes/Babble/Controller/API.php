<?php

/**
 * base api controller class that all public api controller classes should eventually extend from
 */
class Babble_Controller_API extends Controller {
	/**
	 * @var API_Request An instance of API_Request
	 * @access protected
	 */
	protected $api_request = null;

	/**
	 * @var API_Response An instance of API_Response
	 * @access protected
	 */
	protected $api_response = null;

	/**
	 * called before actual method call
	 * !!IMPOTANT AUTHENTICATION HAPPENS IN THIS METHOD!!
	 * @access public
	 * @uses API_Request
	 * @uses API_Response
	 * @throws API_Response_Exception
	 */
	public function before()
	{
		// isntantiate babble
		Babble_API::instance();

		// request and response instances.
		// must force reload since we want the right media type. also pass in existing kohana response (primarily for logging).
		$this->api_request = API_Request::factory('initial', NULL);
		$this->api_response = API_Response::factory('initial', $this->response);

		// kohana request
		$koh_request = $this->api_request->kohana_request();

		// bypass security and use test user if allowed
		$test_user = Kohana::$config->load('babble.test_user');
		if (Kohana::$environment == Kohana::DEVELOPMENT && ! empty($test_user))
		{
			API_User::factory()->login($test_user);
		}
		// if user not authentic, see if we've been passed an Authorization
		// header and attempt to log em in with that.
		elseif ( ! API_User::factory()->logged_in())
		{
			// get the user/key from auth header
			if ( ! $koh_request->headers('authorization'))
			{
				throw new API_Response_Exception('unauthorized user', '401-000');
			}
			list($user, $key) = explode(':', $koh_request->headers('authorization'));

			// get the api user from db.
			// make sure it's an API user, not normal user.
			$user = API_User::factory()->get_user($user);
			if ( ! $user)
			{
				throw new API_Response_Exception('unauthorized user', '401-000');
			}

			// gen a new hash from passed data and private key and check against passed hash.
			// if hashes match then the user has authenticated and we can log them in.
			$query = http_build_query($koh_request->query());
			$url = URL::base($koh_request).$koh_request->uri().( ! empty($query) ? '?'.$query : '');
			$check_key = API_Util::generate_auth_key($user['username'], $user['password'], $url, $_SERVER['REQUEST_METHOD'], (array)$this->api_request->get_request_decoded()->get_data());
			if ( ! empty($key) && $key == $check_key)
			{
				API_User::factory()->login($user['username']);
			}
			else
			{
				throw new API_Response_Exception('unauthorized user', '401-000');
			}
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
			// Execute the "before action" method.
			// You MUST call this before method and it must be before the method call
			// since it's where authentication occurs.
			$this->before();

			// Determine the action to use
			$action = 'action_'.strtolower($_SERVER['REQUEST_METHOD']);

			// If the action doesn't exist, it's a 404
			if ( ! method_exists($this, $action))
			{
				throw new API_Response_Exception('not found', '404-000');
			}

			// Execute the action itself
			$this->{$action}();

			// Execute the "after action" method
			$this->after();

			// check that a response got set.
			if ( ! $this->api_response->resource() || ! $this->api_response->get_code())
			{
				throw new API_Response_Exception('no api response', '500-002');
			}

			// add headers
			foreach ($this->api_response->get_header() as $key => $value)
			{
				$this->response->headers($key, $value);
			}

			// set http status code
			$this->response->status($this->api_response->get_http_code());

			// send out main response from encoded api response
			$this->response->body($this->api_response->get_body_encoded());
			return $this->response;
		}
		catch (API_Response_Exception $e)
		{
			throw $e;
		}
		catch (Exception $e)
		{
			throw new API_Response_Exception('generic API Controller error', '500-000');
		}
	}

}
