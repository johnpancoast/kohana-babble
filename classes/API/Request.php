<?php defined('SYSPATH') or die('No direct script access.');

/**
 * api request handling
 */
abstract class API_Request {
	/**
	 * @var array Instances of drivers
	 * @access private
	 */
	private static $instances = array();

	/**
	 * @var Kohana_Request an instance of kohana request at the time this api request instance was created
	 * @access private
	 */
	private $kohana_request = NULL;

	/**
	 * @var array The passed resource data
	 */
	public $request_resource_data = array();

	/**
	 * constructor. sets kohana request and loads request data.
	 * @access protected
	 * @final
	 */
	protected final function __construct()
	{
		$this->set_kohana_request(Request::current());
		$this->load_request();
	}

	/**
	 * factory method to return a driver object
	 * @access public
	 * @param string $driver The driver object to return
	 * @return API_Request A child of this class (a driver)
	 */
	public static function factory()
	{
		// determine which request driver to load
		$api_method = Request::current()->param('api_method');
		$api_method = strtolower($api_method ? $api_method : Kohana::$config->load('api.driver.request'));
		switch ($api_method)
		{
			case 'post':
			default:
				$driver = 'Post';
				break;
		}

		if ( ! isset(self::$instances[$driver]))
		{
			$class = 'API_Request_'.preg_replace('/[^\w]/', '', $driver);
			self::$instances[$driver] = new $class();
		}
		return self::$instances[$driver];
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
	 * load request
	 * @abstract
	 * @access public
	 * @uses self::$request_resource_id
	 * @uses self::$request_resource_data
	 */
	abstract public function load_request();

	/**
	 * get/generate a request authentication hash key
	 * @access public
	 * @static
	 * @param string $user Public username
	 * @param string $private_key Private key
	 * @param string $url Request url
	 * @param string $method Request method
	 * @param array $post Post data
	 * @return string A hash
	 */
	public static function get_auth_key($user, $private_key, $url, $method, $post = array()) {
		ksort($post);
		$hash_data = $user.'-'.$private_key.'-'.$method.'-'.http_build_query($post);
		return hash_hmac('sha1', $hash_data, $private_key);
	}

}
