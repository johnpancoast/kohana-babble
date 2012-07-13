<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * api config
 */

return array
(
	/**
	 * a response message has a code as it's key and an assoc array of public and optional
	 * private messages. public are what the client will be shown. private are mostly for our
	 * reading.
	 *
	 * < 0 	= errored api calls
	 * 0 	= successful api call with no match (i.e., requested a non-existent id)
	 * > 0 	= successful api call
	 */
	'response_messages' => array(
		// success
		'1'			=> array('public' => 'Success'),

		// success but no match
		'0'			=> array('public' => 'No Match'),

		// errors
		'-99998'	=> array('public' => 'Internal Error', 'private' => 'unknown api response code'),
		'-99999' 	=> array('public' => 'Internal Error', 'private' => 'a terrible error occured'),
	)
);
