<?php

/**
 * API authentication abstraction
 */
abstract class Babble_API_User {
	/**
	 * @var array instances of drivers
	 * @access private
	 * @static
	 */
	private static $instances = array();

	/**
	 * get currently authenticated user
	 * @access public
	 * @abstract
	 */
	abstract public function get_authenticated_user();

	/**
	 * get user
	 * @access public
	 * @abstract
	 * @param string $username Username of user to get
	 * @return mixed False on failure or user record
	 * At the very least this should contain:
	 * array('username' => '<username>', 'password' => '<password>')
	 */
	abstract public function get_user($username = NULL);

	/**
	 * perform a login
	 * @access public
	 * @abstract
	 * @param string $username Username to login
	 * @return bool
	 */
	abstract public function login($username = NULL);

	/**
	 * check if username logged in
	 * @access public
	 * @abstract
	 * @param string $username Username to check if logged in
	 * @return bool
	 */
	abstract public function logged_in($username = NULL);

	/**
	 * constructor. use factory method.
	 */
	protected function __construct() {}

	/**
	 * factory method to load auth implementation
	 * @access public
	 * @static
	 */
	public static function factory($driver = 'Kohana')
	{
		if ( ! isset(self::$instances[$driver]))
		{
			$class = preg_replace('/[^\w]/', '', 'API_User_'.ucfirst(strtolower($driver)));
			self::$instances[$driver] = new $class;
		}

		return self::$instances[$driver];
	}
}
