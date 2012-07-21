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
	/** Success **/
	'1'			=> array('public' => 'Success'),

	/** Success w/o a match **/
	'0'			=> array('public' => 'No record found'),

	/** Errors **/
	// database
	'-7000'		=> array('public' => 'Internal error', 'private' => 'A generic database exception. Perhaps more should be caught or examined.'),
	'-7001'		=> array('public' => 'Duplicate field', 'private' => 'Duplicate DB Key'),

	// kohana orm
	'-8000'		=> array('public' => 'Internal error', 'private' => 'generic exception during ORM interaction'),
	'-8001'		=> array('public' => 'Failed saving: invalid data', 'private' => 'ORM validation failed'),
	'-8002'		=> array('public' => 'Non-existent field passed', 'private' => 'The client attempted to set a value of a non-existen model field'),
	'-8003'		=> array('public' => 'Internal error', 'private' => 'Failed to save the ORM model'),

	// system or unknown generic errors
	'-9000' 	=> array('public' => 'Internal error', 'private' => 'a terrible error occured'),
	'-9001'		=> array('public' => 'Internal error', 'private' => 'unknown api response code'),
	'-9002'		=> array('public' => 'Access denied', 'private' => 'Access denied due to Failed ACL::check_access() call'),
);
