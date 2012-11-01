<?php defined('SYSPATH') or die('No direct script access.');

/**
 * media type - application/json
 */
class API_MediaType_Driver_Application_JSON extends Babble_API_MediaType_Driver_Application_JSON {
	/**
	 * @see API_MediaType::media_type
	 */
	protected $media_type = 'application/json';

	/**
	 * @see parent::_get_data_encoded()
	 */
	protected function _get_data_encoded($data = array(), array $links = array())
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
