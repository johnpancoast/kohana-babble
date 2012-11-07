<?php defined('SYSPATH') or die('No direct script access.');

// public rest api.
// e.g.,
// /api/user/1
// /api/user
$route_path = Kohana::$config->load('babble.route_path');
$route_path = ! empty($route_path) ? $route_path : 'api';
Route::set('babble', $route_path.'/<controller>(/<resource_id>)')
	->defaults(array(
		'directory' => 'Public/API',
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
