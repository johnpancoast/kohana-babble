<?php defined('SYSPATH') or die('No direct script access.');

/**
 * xml api response
 */
class API_MediaType_Default_Application_XML extends API_MediaType {
	/**
	 * @see parent::get_data_encoded()
	 */
	public function get_data_encoded()
	{
		// unimplemented
		// TODO FIXME this should throw a media type exception that is then caught and handled accordingly
		throw new API_Response_Exception('XML response driver unimplemented', '406-001');
	}
}
