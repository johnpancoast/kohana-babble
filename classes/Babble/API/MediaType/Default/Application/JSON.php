<?php defined('SYSPATH') or die('No direct script access.');

/**
 * media type - application/json
 */
class Babble_API_MediaType_Default_Application_JSON extends API_MediaType {
	/**
	 * @see API_MediaType::media_type
	 */
	protected $media_type = 'application/json';

	/**
	 * @see parent::_get_data_encoded()
	 */
	protected function _get_data_encoded($data = array())
	{
		return json_encode($data);
	}

	/**
	 * @see parent::_get_data_decoded()
	 */
	protected function _get_data_decoded($data = NULL)
	{
		return json_decode($data, TRUE);
	}
}
