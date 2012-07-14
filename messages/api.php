<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * api messages
 */
return array
(
	/**
	 * a response message has a code as it's key and an assoc array of public and optional
	 * private messages. public are what the client will be shown. private are mostly for our
	 * reading and perhaps logging in the future.
	 *
	 * 	< 0 	= errored api call
	 * 	0 		= successful api call with no match (i.e., requested a non-existent id)
	 * 	> 0 	= successful api call
	 *
	 * TODO - add ability for substitution from passed developer's exception message
	 */
	// success
	'1'			=> array('public' => 'Success'),

	// success but no match
	'0'			=> array('public' => 'No Match'),

	// errors
	// kohana orm/database
	'-7000'		=> array('public' => 'Internal error', 'private' => 'generic exception during ORM interaction'),
	'-7001'		=> array('public' => 'Failed saving: invalid data', 'private' => 'ORM validation failed'),
	'-7002'		=> array('public' => 'Duplicate field', 'private' => 'Duplicate DB Key'),
	'-7003'		=> array('public' => 'Internal error', 'private' => 'A generic database exception. Perhaps more should be caught or examined.'),

	// system or unknown generic errors
	'-9000' 	=> array('public' => 'Internal error', 'private' => 'a terrible error occured'),
	'-9001'		=> array('public' => 'Internal error', 'private' => 'unknown api response code'),
);
