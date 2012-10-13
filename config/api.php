<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * api config
 */
return array
(
	// the current version of the API. when clients do not pass a version,
	// this is the version that will be loaded.
	'current_version' => '0.0.1',

	// default media types. You can map a media type to the requested media type
	// you wish to be loaded.
	'default_media_types' => array(
		'*/*' => 'application/json',
	),

	// api testing parameters. {@see Controller_Public_API_Testing}.
	// typically you'll set these values in your own site config.
	// {@see application/config/README.md}
	'testing' => array(
		'user' => NULL,
		'pass' => NULL,
		'header' => NULL,
		'curl_debug_file' => NULL,
	),
);
