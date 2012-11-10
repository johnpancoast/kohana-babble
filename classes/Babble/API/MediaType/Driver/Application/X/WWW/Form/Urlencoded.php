<?php defined('SYSPATH') or die('No direct script access.');

/**
 * media type - application/x-www-form-urlencoded
 */
class Babble_API_MediaType_Driver_Application_X_WWW_Form_Urlencoded extends API_MediaType {
	/**
	 * @see API_MediaType::media_type
	 */
	protected $media_type = 'application/x-www-form-urlencoded';

	/**
	 * @see parent::_get_encoded_resource()
	 */
	protected function _get_encoded_resource(Babble_API_Resource $resource)
	{
		return http_build_query($data->as_array());
	}

	/**
	 * @see parent::_get_decoded_resource()
	 */
	protected function _get_decoded_resource($data = NULL)
	{
		parse_str($data, $input);
		$data = isset($input) ? (array)$input : array();
		return new API_Resource($data, NULL, NULL, FALSE);
	}
}
