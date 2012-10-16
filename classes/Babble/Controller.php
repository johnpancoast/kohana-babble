<?php defined('SYSPATH') or die('No direct script access.');

/**
 * base controller class that all controllers should (eventually) extend from.
 * this class transparently extends system/classes/Controller.php
 */
class Babble_Controller extends Kohana_Controller {
	/**
	 * @var array what methods require what roles
	 * @access protected
	 */
	protected $access = array();

	/**
	 * run before anything else
	 */
	public function before()
	{
		parent::before();

		if ( ! $this->check_access())
		{
			echo 'ACCESS DENIED (TODO - eventually this will 404 or throw appropriate 404\'ish exception';
			exit;
		}
	}

	/**
	 * check role access
	 * @access protected
	 * @return bool
	 */
	protected function check_access()
	{
		$check = 'action_'.Request::current()->action();
		$access = array();
		if (isset($this->access[$check]))
		{
			$access = $this->access[$check];
		}
		if (isset($this->access['*']))
		{
			$access = array_merge($access, $this->access['*']);
		}

		return ACL::instance()->check_access($access);
	}
}
