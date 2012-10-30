<?php defined('SYSPATH') or die('No direct script access.');

/**
 * api request handling
 */
class Babble_API_Request {
	/**
	 * @var array Keyed instances
	 * @access private
	 */
	private static $instances = array();

	/**
	 * @var Kohana_Request an instance of kohana request at the time this api request instance was created
	 * @access private
	 */
	private $kohana_request = NULL;

	/**
	 * @var API_MediaType Media type instance
	 * @access private
	 */
	private $media_type = NULL;

	/**
	 * Loads data
	 * @access protected
	 * @final
	 * @param Kohana_Request $kohana_request A kohana request instance
	 */
	protected final function __construct(Kohana_Request $kohana_request = NULL)
	{
		// load kohana request
		$this->kohana_request(($kohana_request ? $kohana_request : Request::current()));

		// load media type via Content-type header
		try
		{
			$header = $this->kohana_request()->headers('content-type');
			$media_type = API_MediaType::factory($header);
			if ($media_type)
			{
				$this->media_type($media_type);
			}
		}
		catch (API_MediaType_Exception_NoConfigClass $e)
		{
			throw new API_Response_Exception('developer set a non-existent config media type class', '415-002');
		}
		catch (API_MediaType_Exception_NoClass $e)
		{
			throw new API_Response_Exception('no media type driver found, assuming 406', '415-001');
		}
		catch (API_MediaType_Exception_Inheritance $e)
		{
			throw new API_Response_Exception('media type class must inherit from API_MediaType', '415-001');
		}
		catch (Exception $e)
		{
			throw new API_Response_Exception('media type class had problems', '500-004');
		}
	}

	/**
	 * factory method to return a driver object
	 * @access public
	 * @param string $driver The driver object to return
	 * @param Kohana_Request $kohana_request A kohana request instance
	 * @return API_Request A child of this class (a driver)
	 */
	public static function factory($instance_key = 'initial', Kohana_Request $kohana_request = NULL)
	{
		if ( ! isset(self::$instances[$instance_key]))
		{
			self::$instances[$instance_key] = new self($kohana_request);
		}

		return self::$instances[$instance_key];
	}

	/**
	 * is this key instantiated
	 * @access public
	 * @param string $instance_key An instance key
	 * @return bool
	 */
	public static function instantiated($instance_key = 'initial')
	{
		return isset(self::$instances[$instance_key]);
	}

	/**
	 * set the kohana request object
	 * @access public
	 * @param Kohana_Request $request the kohana request instance
	 */
	public function set_kohana_request(Kohana_Request $request)
	{
		$this->kohana_request = $request;
	}

	/**
	 * et the kohana request object
	 * @access public
	 * @return Kohana_Request
	 */
	public function get_kohana_request()
	{
		return $this->kohana_request;
	}

	/**
	 * set or get the kohana request object
	 * @access public
	 * @param Kohana_Request $request the kohana request instance
	 * @return Kohana_Request (if request not passed)
	 */
	public function kohana_request(Kohana_Request $request = NULL)
	{
		if ($request)
		{
			return $this->set_kohana_request($request);
		}

		return $this->get_kohana_request();
	}

	/**
	 * set the media type object
	 * @access public
	 * @param API_MediaType $media_type the media type instance
	 */
	public function set_media_type(API_MediaType $media_type)
	{
		$this->media_type = $media_type;
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
	 * @param API_MediaType $media_type the media type instance
	 * @return API_MediaType (if request not passed)
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
	 * get resource data
	 * @access public
	 * @return resource data
	 */
	public function get_request_decoded()
	{
		return $this->media_type ? $this->media_type->get_data_decoded($this->kohana_request()->body()) : array();
	}
}
