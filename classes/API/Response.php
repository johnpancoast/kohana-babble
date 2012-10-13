<?php defined('SYSPATH') or die('No direct script access.');

/**
 * api response handling
 */
class API_Response {
	/**
	 * @var array Keyed instances
	 * @access private
	 */
	private static $instances = array();

	/**
	 * @var API_MediaType Media type instance
	 * @access private
	 */
	private $media_type = NULL;

	/**
	 * @var array Our response array
	 * @access private
	 */
	#private $response = array();

	/**
	 * @var array Response header
	 * @access private
	 */
	private $header = array();

	/**
	 * @var int Response code
	 */
	private $code = NULL;

	/**
	 * @var string Response message
	 */
	private $message = NULL;

	/**
	 * @var mixed Response body
	 */
	private $body = NULL;

	/**
	 * @var array A list of links that get inserted into the response.
	 * We separate this var so code clients can set this separately from the
	 * normal response that may be set (from sometimes differing places).
	 * @access protected
	 */
	private $links = array();

	/**
	 * Loads data
	 * @access protected
	 * @final
	 */
	protected final function __construct()
	{
		// load media type via the requests Accept header
		try
		{
			$header = API_Request::factory()->kohana_request()->headers('accept');
			$this->media_type(API_MediaType::factory($header));
		}
		catch (API_MediaType_Exception_NoConfigClass $e)
		{
			throw new API_Response_Exception('developer set a non-existent config media type class', '406-002');
		}
		catch (API_MediaType_Exception_NoClass $e)
		{
			throw new API_Response_Exception('no media type driver found, assuming 406', '406-001');
		}
		catch (API_MediaType_Exception_Inheritance $e)
		{
			throw new API_Response_Exception('media type class must inherit from API_MediaType', '406-000');
		}
	}

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
			self::$instances[$instance_key] = new self;
		}

		return self::$instances[$instance_key];
	}

	/**
	 * get encoded response
	 * @return string
	 */
	public function get_body_encoded()
	{
		return $this->media_type->get_data_encoded($this->get_body());
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
	 * get body
	 * @access public
	 * @return {@see self::$body}
	 */
	public function get_body()
	{
		return $this->body;
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
	public function get_code()
	{
		return $this->code;
	}

	/**
	 * get response http code
	 * @access public
	 * @return int response http code
	 */
	public function get_http_code()
	{
		return substr($this->get_code(), 0, 3);
	}

	/**
	 * get message
	 * @return string self::message
	 */
	public function get_message()
	{
		return $this->message;
	}

	/**
	 * set response
	 * @access public
	 * @param string $code Response code. Should match our codes in config/base/api.php.
	 * @param mixed $body Optional body. Typically an array.
	 * @throws API_Response_Exception Upon error
	 */
	public function set_response($code, $body = null) {
		$message = Kohana::message('api', 'responses.'.$code.'.public');

		// if we were sent an invalid code, we must throw a new
		// API_Response_Exception so the error gets logged. however, we must
		// catch it as well in case the code originated from an
		// API_Response_Exception in the first place.
		if ( ! $message)
		{
			try 
			{
				throw new API_Response_Exception('unknown api response code ('.$code.')', '500-001');
			}
			catch (Exception $e)
			{
				$code = '500-001';
				$message = Kohana::message('api', 'responses.'.$code.'.public');
			}
		}

		// set code and message
		$this->code = $code;
		$this->message = $message;

		// set response header if we have title set in config
		$resp_header = Kohana::$config->load('api.header.response_header_title');
		if ( ! empty($resp_header))
		{
			$this->add_header($resp_header, $this->code.'; '.$this->message);
		}

		// set the api result if we have one and the http status codes are in 200's
		$http_code = $this->get_http_code($code);
		if ($body && $http_code >= 200 && $http_code < 300)
		{
			$this->body = $body;
		}

		// allow chaining
		return $this;
	}

	/**
	 * set the media type object
	 * @access public
	 * @param API_MediaType $media_type The media type instance
	 */
	public function set_media_type(API_MediaType $media_type)
	{
		$this->media_type = $media_type;

		// if we have a valid media type value, set Content-Type header
		$type = $this->media_type->get_media_type();
		if ($type && ! empty($type))
		{
			$this->add_header('Content-Type', $type);
		}
	}

	/**
	 * get the media type object
	 * @access public
	 * @return API_MediaType
	 */
	public function get_media_type()
	{
		return $this->media_type;
	}

	/**
	 * set or get the media type object
	 * @access public
	 * @param API_MediaType $media_type The media type instance
	 * @return API_MediaType (if $media_type not passed)
	 */
	public function media_type(API_MediaType $media_type = NULL)
	{
		if ($media_type)
		{
			return $this->set_media_type($media_type);
		}

		return $this->get_media_type();
	}
}
