<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Request. This transparently extends system/classes/Request.php
 * {@see Request}
 * {@see Kohana_Request}
 */
class Request extends Kohana_Request {
	// was this request initiated from an API call
	public $is_api_request = FALSE;
} // End Request
