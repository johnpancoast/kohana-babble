<?php defined('SYSPATH') or die('No direct script access.');

/**
 * media type - application/json
 */
class Babble_API_MediaType_Driver_Application_JSON extends API_MediaType {
	/**
	 * @see API_MediaType::media_type
	 */
	protected $media_type = 'application/json';

	/**
	 * @see parent::_get_encoded_resource()
	 */
	protected function _get_encoded_resource(Babble_API_Resource $resource)
	{
		return json_encode($resource->as_array());
	}

	/**
	 * @see parent::_get_decoded_resource()
	 */
	protected function _get_decoded_resource($data = NULL)
	{
		return new Babble_API_Resource;
	}
}
