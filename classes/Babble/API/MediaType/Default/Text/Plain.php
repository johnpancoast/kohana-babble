<?php defined('SYSPATH') or die('No direct script access.');

/**
 * media type - text/plain
 */
class Babble_API_MediaType_Default_Text_Plain extends API_MediaType {
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
	 * @see parent::_get_data_encoded()
	 */
	protected function _get_data_encoded($data = array())
	{
		// just print key=value pairs separated by comma and new line
		if (is_array($data))
		{
			$ret = array();
			foreach ($data as $k => $v)
			{
				$ret[] = $k.$this->value_sep.$v;
			}
			return implode($this->set_sep, $ret);
		}
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
