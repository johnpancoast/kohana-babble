<?php defined('SYSPATH') or die('No direct script access.');

class Babble_Controller_Public_Index extends Controller {

	public function action_index()
	{
		$this->response->body('hello, world!');
	}
}
