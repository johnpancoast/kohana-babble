<?php defined('SYSPATH') OR die('No direct script access.');

return array(
	// Leave this alone
	'modules' => array(
		// This should be the path to this modules userguide pages, without the 'guide/'. Ex: '/guide/modulename/' would be 'modulename'
		'babble' => array(
			// Whether this modules userguide pages should be shown
			'enabled' => TRUE,

			// The name that should show up on the userguide index page
			'name' => 'Babble',

			// A short description of this module, shown on the index page
			'description' => 'A REST API framework',

			// Copyright message, shown in the footer for this module
			'copyright' => '&copy; 2012-2013 John Pancoast',
		)
	)
);
