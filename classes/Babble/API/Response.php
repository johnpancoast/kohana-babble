<?php defined('SYSPATH') or die('No direct script access.');

/**
 * api response handling
 */
class Babble_API_Response {
	/**
	 * @var array Keyed instances
	 * @access private
	 */
	private static $instances = array();

	/**
	 * @var Kohana_Response an instance of kohana response at the time this api response instance was created
	 * @access private
	 */
	private $kohana_response = NULL;

	/**
	 * @var API_MediaType Media type instance
	 * @access private
	 */
	private $media_type = NULL;

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
	 * @var Babble_API_Resource A resource for the response
	 * @access private
	 */
	private $resource = NULL;

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
	protected final function __construct(Kohana_Response $kohana_response = NULL)
	{
		$this->kohana_response($kohana_response);

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
		catch (Exception $e)
		{
			throw new API_Response_Exception('media type class had problems', '500-004');
		}
	}

	/**
	 * factory method to return a driver object
	 * @access public
	 * @static
	 * @param string $instance_key The keyed instance to return
	 * @return API_Request A child of this class (a driver)
	 */
	public static function factory($instance_key = 'initial', Kohana_Response $kohana_response = NULL)
	{
		if ( ! isset(self::$instances[$instance_key]))
		{
			self::$instances[$instance_key] = new self($kohana_response);
		}

		return self::$instances[$instance_key];
	}

	/**
	 * Set resource. Note that you should generally use set_response over this method.
	 * @access protected
	 * @param Babble_API_Resource A babble resource
	 */
	public function set_resource(Babble_API_Resource $resource)
	{
		$this->resource = $resource;
	}

	/**
	 * get resource
	 * @access public
	 * @return Babble_API_Resource
	 */
	public function get_resource()
	{
		return $this->resource;
	}

	/**
	 * set/get resource
	 * @access public
	 * @param Babble_API_Resource $resource Babble api resource
	 * @return mixed Babble resource if $resource not passed
	 */
	public function resource(Babble_API_Resource $resource = NULL)
	{
		if ($resource)
		{
			return $this->set_resource($resource);
		}
		return $this->get_resource();
	}

	/**
	 * set code
	 * @access public
	 * @param mixed $code Response code
	 */
	public function set_code($code)
	{
		$this->code = $code;
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
	 * set/get code
	 * @access public
	 * @param mixed $code The code
	 * @return mixed Code If $code not passed.
	 */
	public function code($code = NULL)
	{
		if ($code)
		{
			return $this->set_code($code);
		}
		return $this->get_code();
	}

	/**
	 * get encoded response
	 * @return string
	 */
	public function get_body_encoded()
	{
		return $this->media_type->get_encoded_resource($this->get_resource());
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
	 * get header. note that generally you should call this after set_response() has been called
	 * since that method may set headers.
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
	 * get response http code
	 * @access public
	 * @return int response http code
	 */
	public function get_http_code()
	{
		return substr($this->get_code(), 0, 3);
	}


	/**
	 * set response
	 * @access public
	 * @param string $code Response code. Should match our codes in messages/babble.php..
	 * @param mixed $response Instance of Babble_API_Resource or a string message
	 * @throws API_Response_Exception Upon error
	 */
	public function set_response($code, $response = NULL) {
		$msg_public = Kohana::message('babble', 'responses.'.$code.'.public');
		$msg_hint = Kohana::message('babble', 'responses.'.$code.'.public_hint');

		// if we were sent an invalid code, we must throw a new
		// API_Response_Exception so the error gets logged. however, we must
		// catch it as well in case the code originated from an
		// API_Response_Exception in the first place.
		if ( ! $msg_public)
		{
			try 
			{
				throw new API_Response_Exception('unknown api response code ('.$code.')', '500-001');
			}
			catch (Exception $e)
			{
				$code = '500-001';
				$msg_public = Kohana::message('babble', 'responses.'.$code.'.public');
			}
		}

		// set code
		$this->code($code);

		// set body.
		// 100's & 200's we just set the body.
		// 300's and greater we set our own body.
		if (substr($code, 0, 1) <= 2)
		{
			if (is_string($response))
			{
				$data = array(
					'http_code' => $this->get_http_code(),
					'http_message' => $msg_public,
					'code' => $this->get_code(),
					'message' => $response,
				);
				$resp_body = new API_Resource($data);
			}
			// just assume we have already have a resource or resource collection object
			else
			{
				$resp_body = $response;
			}
		}
		else
		{
			$data = array(
				'http_code' => $this->get_http_code(),
				'http_message' => $msg_public,
				'code' => $this->get_code(),
				'message' => ($msg_hint ? $msg_hint : $msg_public),
			);
			$resp_body = new API_Resource($data);
		}
		$this->resource($resp_body);

		// set response header if we have title set in config
		$resp_header = Kohana::$config->load('babble.header.response_header_title');
		if ( ! empty($resp_header))
		{
			$this->add_header($resp_header, $this->code.'; '.( ! empty($msg_hint) ? $msg_hint : $msg_public));
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

	/**
	 * set the kohana response object
	 * @access public
	 * @param Kohana_Response $response the kohana response instance
	 */
	public function set_kohana_response(Kohana_Response $response)
	{
		$this->kohana_response = $response;
	}

	/**
	 * et the kohana response object
	 * @access public
	 * @return Kohana_Response
	 */
	public function get_kohana_response()
	{
		return $this->kohana_response;
	}

	/**
	 * set or get the kohana response object
	 * @access public
	 * @param Kohana_Response $response the kohana response instance
	 * @return Kohana_Response (if response not passed)
	 */
	public function kohana_response(Kohana_Response $response = NULL)
	{
		if ($response)
		{
			return $this->set_kohana_response($response);
		}

		return $this->get_kohana_response();
	}

}
