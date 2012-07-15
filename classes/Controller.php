<?php defined('SYSPATH') or die('No direct script access.');

/**
 * base controller class that all controllers should (eventually) extend from.
 * this class transparently extends system/classes/Controller.php
 */
class Controller extends Kohana_Controller {
	protected $access = array();

	public function before()
	{
		parent::before();
		$this->check_access();
	}

	private function check_access()
	{
		$check = 'action_'.$this->request->action();
		$access = array();
		if (isset($this->access[$check]))
		{
			$access[] = $this->access[$check];
		}
		if (isset($this->access['*']))
		{
			$access[] = array_merge($access, $this->access['*']);
		}
		$allowed = ACL::instance()->check_access($access);
		if ( ! $allowed)
		{
			echo 'ACCESS DENIED (eventually this will 404 or for APIs return an error code)';
			exit;
		}
	}
}
