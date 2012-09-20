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
	 * @access private
	 */
	private $response = array();

	/**
	 * @var array Response header
	 * @access private
	 */
	private $header = array();

	/**
	 * @var array A list of links that get inserted into the response.
	 * We separate this var so code clients can set this separately from the
	 * normal response that may be set (from sometimes differing places).
	 * @access protected
	 */
	private $links = array();
	/**
	 * get an encoded response messsage
	 * @param string $response The message to encode and return
	 */
	abstract public function get_response_encoded();

	/**
	 * factory method to return a driver object
	 * @access public
	 * @static
	 * @param string $instance_key The keyed instance to return
	 * @return API_Request A child of this class (a driver)
	 */
	public static function factory($instance_key = 'initial')
	{
		if ( ! isset(self::$instances[$instance_key]))
		{
			// the class we're looking for that will handle response.
			$media_drtver_class = NULL;

			// kohana request that initialized us
			$koh_request = API_Request::factory()->kohana_request();

			// accept header
			$accept = $koh_request->headers('accept');

			// content types from accept header
			$content_types = API_Util::get_content_type_set($accept);

			// loop content types and find a class
			foreach ($content_types as $type)
			{
				if (class_exists($type['real']['class']))
				{
					$media_drtver_class = $type['real']['class'];
					break;
				}
				elseif (class_exists($type['real']['default_class']))
				{
					$media_drtver_class = $type['real']['default_class'];
					break;
				}
			}

			// if we found no class, then we have nothing to respond with
			if ( ! $media_drtver_class)
			{
				throw new API_Response_Exception('no response driver found, assuming 406', '406-000');
			}

			self::$instances[$instance_key] = new $media_drtver_class();
		}

		return self::$instances[$instance_key];
	}

	/**
	 * add a link
	 * @param string $link The link
	 * @param string $rel The link relation
	 * @param string $title The link title
	 */
	public function add_link($link, $rel = NULL, $title = NULL)
	{
		$this->links[] = array('link' => $link, 'rel' => $rel, 'title' => $title);
	}

	/**
	 * get links
	 * @return array Links
	 */
	public function get_links()
	{
		return $this->links;
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
}
