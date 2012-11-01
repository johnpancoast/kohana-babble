<?php defined('SYSPATH') or die('No direct script access.');

/**
 * media type - NULLn
 */
class API_MediaType_DriverDefault extends Babble_API_MediaType_DriverDefault {
	/**
	 * @see API_MediaType::media_type
	 */
	protected $media_type = NULL;

	/**
	 * @see parent::_get_data_encoded()
	 */
	protected function _get_data_encoded($data = array(), array $links = array())
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
