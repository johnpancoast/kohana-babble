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
	 * factory method to return a driver object
	 * @access public
	 * @param string $driver The driver object to return
	 * @return API_Request A child of this class (a driver)
	 */
	public static function factory($driver = 'JSON')
	{
		if ( ! isset(self::$instances[$driver]))
		{
			$class = 'API_Request_'.preg_replace('/[^\w]/', '', $driver);
			self::$instances[$driver] = new $class;
		}
		return self::$instances[$driver];
	}

	/**
	 * encode a response messsage
	 * @param string $response The message to encode
	 */
	abstract public function get_encoded_response($response);
}
