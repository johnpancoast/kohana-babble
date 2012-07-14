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
	 */
	protected $response = array();

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
			$class = 'API_Response_Driver_'.preg_replace('/[^\w]/', '', $driver);
			self::$instances[$driver] = new $class();
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
	 * set response
	 * @access public
	 * @param string $code Response code. Should match our codes in config/base/api.php.
	 * @param mixed $result Option result message. Typically an array.
	 * @throws API_Response_Exception Upon error
	 */
	public function set_response($code, $result = null) {
		$msgs = Kohana::$config->load('api.response_messages');

		// if we were sent an invalid code, we must throw a new API_Response_Exception so
		// the error gets logged. however, we must catch it as well
		// in case the wrong code originated from an API_Response_Exception
		// in the first place.
		if ( ! in_array($code, array_keys($msgs)))
		{
			try 
			{
				throw new API_Response_Exception('unknown api response code', '-9001');
			}
			catch (Exception $e)
			{
				$code = '-9001';
			}
		}
		$this->response = array(
			'code' => $code,
			'message' => $msgs[$code]['public'],
		);
		if ($result)
		{
			$this->response['result'] = $result;
		}
	}

	/**
	 * get an encoded response messsage
	 * @param string $response The message to encode and return
	 */
	abstract public function get_encoded_response();
}
