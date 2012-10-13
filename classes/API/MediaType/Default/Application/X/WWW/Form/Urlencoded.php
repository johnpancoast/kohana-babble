<?php defined('SYSPATH') or die('No direct script access.');

/**
 * media type - application/x-www-form-urlencoded
 */
class API_MediaType_Default_Application_X_WWW_Form_Urlencoded extends API_MediaType {
	/**
	 * @see API_MediaType::media_type
	 */
	protected $media_type = 'application/x-www-form-urlencoded';

	/**
	 * @see parent::get_data_encoded()
	 */
	public function get_data_encoded(array $data = array())
	{
		return http_build_query($data);
	}

	/**
	 * @see parent::get_data_decoded()
	 */
	public function get_data_decoded($data = NULL)
	{
		parse_str($data, $input);
		return isset($input) ? (array)$input : array();
	}
}
