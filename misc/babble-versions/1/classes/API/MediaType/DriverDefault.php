<?php defined('SYSPATH') or die('No direct script access.');

/**
 * media type - NULL
 */
class API_MediaType_DriverDefault extends Babble_API_MediaType_DriverDefault {
	/**
	 * @see API_MediaType::media_type
	 */
	protected $media_type = NULL;

	/**
	 * @see parent::_get_encoded_resource()
	 */
	protected function _get_encoded_resource(Babble_API_Resource $resource)
	{
		return NULL;
	}

	/**
	 * @see parent::_get_decoded_resource()
	 */
	protected function _get_decoded_resource($data = NULL)
	{
		return new Babble_API_Resource;
	}

	/**
	 * @see parent::_get_encoded_resources()
	 */
	protected function _get_encoded_resources(Babble_API_Resource_Collection $resources)
	{
		return NULL;
	}

	/**
	 * @see parent::_get_decoded_resources()
	 */
	protected function _get_decoded_resources($data = NULL)
	{
		return new Babble_API_Resource_Collection;
	}
}
