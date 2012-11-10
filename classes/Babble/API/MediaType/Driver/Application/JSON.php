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
		return $this->get_data_resource(json_decode($data));
	}

	private function get_data_resource($data)
	{
		$rsc = new API_Resource;
		$objdata = array();
		foreach ($data AS $k => $v)
		{
			switch ($k)
			{
				// links ignored when receiving data
				case '_links':
					break;
				// recursively add embedded objects
				case '_embedded':
					foreach ($v AS $rel => $obj)
					{
						if (is_array($obj))
						{
							foreach ($obj AS $o)
							{
								$rsc->add_embedded_resource($rel, $this->get_data_resource($o));
							}
						}
						else
						{
							$rsc->add_embedded_resource($rel, $this->get_data_resource($obj));
						}
					}
					break;
				// everything else is object data
				default:
					$objdata[$k] = $v;
					break;
			}
		}
		$rsc->set_data($objdata);
		return $rsc;
	}
}
