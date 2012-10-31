<?php defined('SYSPATH') or die('No direct script access.');

// public rest api. routes to api frontend.
// e.g.,
//     /api/user/1
//     /api/user
Route::set('api', 'api/<resource_controller>(/<resource_id>)')
	->defaults(array(
		'directory' => 'Public',
		'controller' => 'APIFrontend',
		'action'     => 'index',
	));

// route for testing above API. only create this is in DEV mode.
if (Kohana::$environment == Kohana::DEVELOPMENT)
{
	Route::set('testapi', '<testapi>', array('testapi' => 'testapi.*'))
		->defaults(array(
			'directory' => 'Public/API/Test',
			'controller' => 'Index',
			'action'     => 'index',
		));
}
