<?php defined('SYSPATH') or die('No direct script access.');

/**
 * api user implementation - kohana
 */
class Babble_API_User_Kohana extends API_User {
	public function get_authenticated_user()
	{
		return Auth::instance()->get_user();
	}

	public function get_user($username = NULL)
	{
		$user = ORM::factory('user')
			->where('username', '=', $username)
			->where('api_user', '=', 1)
			->find();
		if ( ! $user->loaded())
		{
			return FALSE;
		}
		return $user->as_array();
	}

	public function login($username = NULL)
	{
		if ( ! $username)
		{
			return FALSE;
		}
		return Auth::instance()->force_login($username);
	}

	public function logged_in($username = NULL)
	{
		if ( ! $username)
		{
			return FALSE;
		}
		return Auth::instance()->logged_in($username);
	}
}
