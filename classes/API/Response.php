<?php defined('SYSPATH') or die('No direct script access.');

/**
 * api response handling
 */
abstract class API_Response {
	/**
	 * @var array Instances of {@see API_Response} drivers
	 * @access private
	 */
	private static $instances = array();

	/**
	 * @var array Our response array
	 * @access protected
	 */
	protected $response = array();

	/**
	 * @var array header
	 * @access protected
	 */
	private $header = array();

	/**
	 * factory method to return a driver object
	 * @access public
	 * @static
	 * @param string $driver The driver object to return
	 * @return API_Request A child of this class (a driver)
	 */
	public static function factory()
	{
		// determine which driver to load
		// FIXME change this to get method via the Accept header
		$api_method = Request::current()->param('api_method');
		$api_method = strtolower($api_method ? $api_method : Kohana::$config->load('api.driver.response'));

		// for now just set content type from Accept header
		$content_type = Request::current()->headers('accept');

		switch ($api_method)
		{
			case 'xml':
				$driver = 'XML';
				$content_type = $content_type ? $content_type : 'application/xml';
				break;
			case 'namevalue':
			case 'nv':
				$content_type = $content_type ? $content_type : 'text/html';
				$driver = 'NameValue';
				break;
			case 'json':
			default:
				$content_type = $content_type ? $content_type : 'application/json';
				$driver = 'JSON';
				break;
		}

		if ( ! isset(self::$instances[$driver]))
		{
			$class = 'API_Response_Driver_'.preg_replace('/[^\w]/', '', $driver);
			self::$instances[$driver] = new $class();

			// always default the charset to utf8
			self::$instances[$driver]->add_header('Content-Type', $content_type.'; charset=utf8');
		}
		return self::$instances[$driver];
	}

	/**
	 * get response
	 * @access public
	 * @return {@see self::$response}
	 */
	public function get_response()
	{
		return $this->response;
	}

	/**
	 * add a header line
	 * @param string $key header key
	 * @param string $key header value
	 * @access protected
	 */
	protected function add_header($key, $value)
	{
		$this->header[$key] = $value;
	}

	/**
	 * get header
	 * @param string $key header key to return
	 * @return array all headers or just one line if $key provided
	 */
	public function get_header($key = NULL)
	{
		if ($key)
		{
			return $this->header[$key];
		}
		else
		{
			return $this->header;
		}
	}

	/**
	 * get response code
	 * @access public
	 * @return string response code
	 */
	public function get_response_code()
	{
		$code = NULL;
		$response = $this->get_response();
		if ( ! empty($response))
		{
			$code = $this->response['code'];
		}
		return $code;
	}

	/**
	 * get response http code
	 * @access public
	 * @return int response http code
	 */
	public function get_response_http_code()
	{
		return substr($this->get_response_code(), 0, 3);
	}

	/**
	 * set response
	 * @access public
	 * @param string $code Response code. Should match our codes in config/base/api.php.
	 * @param mixed $result Option result message. Typically an array.
	 * @throws API_Response_Exception Upon error
	 */
	public function set_response($code, $result = null) {
		$message = Kohana::message('api', 'responses.'.$code.'.public');

		// if we were sent an invalid code, we must throw a new
		// API_Response_Exception so the error gets logged. however, we must
		// catch it as well in case the code originated from an
		// API_Response_Exception in the first place.
		if ( ! $message)
		{
			try 
			{
				throw new API_Response_Exception('unknown api response code', '500-001');
			}
			catch (Exception $e)
			{
				$code = '500-001';
				$message = Kohana::message('api', 'responses.'.$code.'.public');
			}
		}

		$this->response = array(
			'code' => $code,
			'message' => $message,
		);

		$http_code = substr($code, 0, 3);

		// set the api result if we have one and the http status codes are in 200's
		if ($result && $http_code >= 200 && $http_code < 300)
		{
			$this->response['result'] = $result;
		}

		// allow chaining
		return $this;
	}

	/**
	 * get an encoded response messsage
	 * @param string $response The message to encode and return
	 */
	abstract public function get_encoded_response();
}
