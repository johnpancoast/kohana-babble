<?php defined('SYSPATH') or die('No direct script access.');

/**
 * media type - text/plain
 */
class API_MediaType_Default_Text_Plain extends API_MediaType {
	/**
	 * @see API_MediaType::media_type
	 */
	protected $media_type = 'text/plain';

	/**
	 * @see parent::_get_data_encoded()
	 */
	protected function _get_data_encoded($data = array())
	{
		return $data;
	}

	/**
	 * @see parent::_get_data_decoded()
	 */
	protected function _get_data_decoded($data = NULL)
	{
		return $data;
	}
}
