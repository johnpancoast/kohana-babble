<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Public_Index extends Controller {

	public function action_index()
	{
		$this->response->body('hello, world!');
	}

	/**
	 * just here to demonstrate. we access controller methods using pretty urls by default.
	 * example.com/index/louder
	 */
	public function action_louder()
	{
		$this->response->body('WUSSSSSUP WORLD!');
	}

} // End Index
