<?php defined('SYSPATH') or die('No direct script access.');

/**
 * media type - text/plain
 */
class Babble_API_MediaType_Driver_Text_Plain extends API_MediaType {
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
	 * FIXME - Does not support embedded objects
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
		// at present our api doesn't support passing in text/*
		return new Babble_API_Resource;
	}
}
