<?php defined('SYSPATH') or die('No direct script access.');

/**
 * media type - text/html
 *
 * similar handling to text/plain, so extend it
 */
class API_MediaType_Driver_Text_Html extends Babble_API_MediaType_Driver_Text_Html {
	/**
	 * @see API_MediaType::media_type
	 * @access protected
	 */
	protected $media_type = 'text/html';

	/**
	 * @var string Set separator
	 * @access protected
	 */
	protected $set_sep = "<br/>\n";
}
