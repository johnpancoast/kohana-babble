<?php defined('SYSPATH') or die('No direct script access.');

/**
 * media type - application/json
 */
class API_MediaType_Default_Application_JSON extends API_MediaType {
	/**
	 * @see parent::get_data_encoded()
	 */
	public function get_data_encoded(array $data = array())
	{
		return json_encode($data);
	}

	/**
	 * @see parent::get_data_decoded()
	 */
	public function get_data_decoded($data = NULL)
	{
		return json_decode($data, TRUE);
	}
}
