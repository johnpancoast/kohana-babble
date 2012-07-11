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

	private $request = array();
	public $request_id = null;
	public $request_post = array();

	protected final function __construct()
	{
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

	abstract public function load_request();
}
