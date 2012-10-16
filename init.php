<?php defined('SYSPATH') or die('No direct script access.');

// public rest api. routes to api frontend.
// e.g.,
//     /api/user/get/1
Route::set('api', 'api/<controller>(/<resource_id>)')
	->defaults(array(
		'directory' => 'Public/API',
		'controller' => 'Index',
		'action'     => 'index',
	));

// route for testing above API. only create this is in DEV mode.
if (Kohana::$environment == Kohana::DEVELOPMENT)
{
	Route::set('testapi', '<testapi>', array('testapi' => 'testapi.*'))
		->defaults(array(
			'directory' => 'Public/API',
			'controller' => 'Testing',
			'action'     => 'index',
		));
}

