<?php defined('SYSPATH') or die('No direct script access.');

/**
 * media type - text/plain
 */
class API_MediaType_Driver_Text_Plain extends Babble_API_MediaType_Driver_Text_Plain {
	/**
	 * @see API_MediaType::media_type
	 */
	protected $media_type = 'text/plain';

	/**
	 * @var string Key/value separator
	 * @access protected
	 */
	protected $value_sep = ' = ';

	/**
	 * @var string Set separator
	 * @access protected
	 */
	protected $set_sep = ",\n";

	/**
	 * @see parent::_get_encoded_resource()
	 */
	protected function _get_encoded_resource(Babble_API_Resource $resource)
	{
		// just print key=value pairs separated by comma and new line
		$ret = array();
		foreach ($resource->get_data() AS $k => $v)
		{
			$ret[] = $k.$this->value_sep.$v;
		}
		return implode($this->set_sep, $ret);
	}

	/**
	 * @see parent::_get_decoded_resource()
	 */
	protected function _get_decoded_resource($data = NULL)
	{
		return new Babble_API_Resource;
	}
}
