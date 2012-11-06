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

	/**
	 * @see parent::_get_encoded_resources()
	 */
	protected function _get_encoded_resources(Babble_API_Resource_Collection $resources)
	{
		$ret = array();
		foreach ($resources AS $rsc)
		{
			$ret[] = $rsc->as_array();
		}
		return json_encode($ret);
	}

	/**
	 * @see parent::_get_decoded_resources()
	 */
	protected function _get_decoded_resources($data = NULL)
	{
		return new Babble_API_Resource_Collection;
	}
}
