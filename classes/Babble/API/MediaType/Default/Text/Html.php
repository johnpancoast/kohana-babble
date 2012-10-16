<?php defined('SYSPATH') or die('No direct script access.');

/**
 * media type - text/html
 *
 * similar handling to text/plain, so extend it
 */
class Babble_API_MediaType_Default_Text_Html extends API_MediaType_Default_Text_Plain {
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
