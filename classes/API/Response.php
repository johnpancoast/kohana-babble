<?php defined('SYSPATH') or die('No direct script access.');

/**
 * api response handling
 */
abstract class API_Response {
	/**
	 * @var array Instances of drivers
	 * @access private
	 */
	private static $instances = array();
	protected $response = array();

	/**
	 * factory method to return a driver object
	 * @access public
	 * @param string $driver The driver object to return
	 * @return API_Request A child of this class (a driver)
	 */
	public static function factory()
	{
		// determine which driver to load
		$api_method = Request::current()->param('api_method');
		$api_method = strtolower($api_method ? $api_method : Kohana::$config->load('api.driver.response'));
		switch ($api_method)
		{
			case 'xml':
				$driver = 'XML';
				break;
			case 'namevalue':
			case 'nv':
				$driver = 'NameValue';
				break;
			case 'json':
			default:
				$driver = 'JSON';
				break;
		}

		if ( ! isset(self::$instances[$driver]))
		{
			$class = 'API_Response_'.preg_replace('/[^\w]/', '', $driver);
			self::$instances[$driver] = new $class();
		}
		return self::$instances[$driver];
	}

	public function set_response($response_status, $response_code, $response_message) {
		$this->response = array(
			'status' => $response_status,
			'code' => $response_code,
			'message' => $response_message
		);
	}

	/**
	 * encode a response messsage
	 * @param string $response The message to encode
	 */
	abstract public function get_encoded_response();
}
