<?php defined('SYSPATH') or die('No direct script access.');

/**
 * text response
 */
class API_MediaType_Default_Text_Plain extends API_MediaType {
	/**
	 * @see parent::get_response_encoded()
	 */
	public function get_response_encoded()
	{
		// we just assume our code has set some text as opposed to an array
		return $this->get_response();
	}
}