<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * api config
 */
return array
(
	'default_version' => '0.0.1',

	// TODO add comment
	'default_content_types' => array(
		//'application/vnd.companyname.appname+json' => 'application/json',
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
