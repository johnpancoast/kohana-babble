<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Public_Test extends Controller {

	public function action_index()
	{
		$this->response->body('hello, world!');
	}
}
