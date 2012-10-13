<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * api config
 */
return array
(
	/**
	 * [current_version]
	 *
	 * the current version of the API. When clients do not pass a version,
	 * this is the version used where applicable..
	 */
	'current_version' => '0.0.1',

	/**
	 * [default_media_types]
	 *
	 * You can map a requested Accept header media type to the media type
	 * you wish to be loaded.
	 */
	'default_media_types' => array(
		'*/*' => 'application/json',
	),

	/**
	 * [test_user]
	 *
	 * !!WARNING!! Be careful with this value.
	 *
	 * When test_user is specified and your environment is in Kohana::DEVELOPMENT,
	 * this user will be automatically logged in as the api user for the request.
	 * Bypasses security measures when in Kohana::DEVELOPMENT mode _only_ but you
	 * should still be careful with this setting and your environments.
	 */
	'test_user' => NULL,

	/**
	 * header related values
	 */
	'header' => array(
		/**
		 * [header.response_header_title]
		 *
		 * if this value is specified, a response header of this name will be set.
		 * it will contain the api response code and message. this can
		 * be useful to clients.
		 *
		 * Be mindful of what you name this header title. Do not use existing
		 * response header titles. Do not prepend with "X-".
		 * {@see http://tools.ietf.org/html/rfc6648}
		 */
		'response_header_title' => NULL,
	),

	/**
	 * [testing]
	 *
	 * api testing parameters. {@see Controller_Public_API_Testing}.
	 */
	'testing' => array(
		'user' => NULL,
		'pass' => NULL,
		'header' => NULL,
		'curl_debug_file' => NULL,
	),
);
