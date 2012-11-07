<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * api config
 */
return array
(
	/**
	 * [versions]
	 *
	 * All API versions. The keyed version string should _only_ contain numbers and .'s.
	 */
	'versions' => array(
		//'1' => APPPATH.'babble-versions/1',
	),

	/**
	 * [current_version]
	 *
	 * the current version of the API. When clients do not pass a version,
	 * this is the version used where applicable..
	 */
	'current_version' => '1',

	/**
	 * [default_media_types]
	 *
	 * You can map a requested Accept header media type to the media type
	 * you wish to be loaded for it. We default to responding with json.
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
		 * be useful to clients who are debugging.
		 *
		 * Be mindful of what you name this header title. Do not use existing
		 * response header titles. Do not prepend with "X-".
		 * {@see http://tools.ietf.org/html/rfc6648}
		 */
		'response_header_title' => NULL,
	),

	/**
	 * [debug]
	 *
	 * Log debugging info. This _should not_ be done in production environments as
	 * your logs will fill up quickly.
	 */
	'debug' => FALSE,

	/**
	 * [route_path]
	 *
	 * This is the path in the URI that gets routed to a controller. For example, if
	 * this is set to 'api', then you'll trigger your route with http://example.com/api.
	 * {@see init.php}
	 */
	'route_path' => 'api',

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
