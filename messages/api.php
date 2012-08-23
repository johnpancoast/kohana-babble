<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * api messages
 */
return array
(
	'responses' => array(
		/**
		 * a response message has a code as it's key and an assoc array of public and optional
		 * private messages. public are what the client will be shown. private are mostly for our
		 * reading and perhaps logging in the future.
		 *
		 * keys are http status codes followed by a - followed by a 3 digit internal identifier.
		 * The internal identifiers should be broken into logical subsections.
		 * See: http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
		 *
		 * TODO - add ability for substitution from passed developer's exception message
		 */

		#########################################
		## SUCCESSFUL (http codes in 200's)
		#########################################
		'200-000'		=> array('public' => 'Ok'),

		#########################################
		## USER ERRORS (http codes in 400's)
		#########################################
		// bad request - database
		'400-000'		=> array('public' => 'Duplicate field', 'private' => 'Duplicate DB Key'),

		// bad request - orm
		'400-100'		=> array('public' => 'Failed saving: invalid data', 'private' => 'ORM validation failed'),
		'400-101'		=> array('public' => 'Non-existent field passed', 'private' => 'The client attempted to set a value of a non-existen model field'),

		// unauthorized
		'401-000'		=> array('public' => 'Access denied', 'private' => 'Access denied due to failed authentication'),
		'401-001'		=> array('public' => 'Access denied', 'private' => 'Access denied due to failed access'),

		// not found
		'404-000'		=> array('public' => 'The requested URL was not found', 'private' => 'The requested URL was not found'),

		#########################################
		## SYSTEM ERRORS (http codes in 500's)
		#########################################
		// internal error - generic
		'500-000' 	=> array('public' => 'Internal error', 'private' => 'A terrible error occured'),
		'500-001'	=> array('public' => 'Internal error', 'private' => 'Unknown API response code'),

		// internal error - database
		'500-100'	=> array('public' => 'Internal error', 'private' => 'A generic database exception. Perhaps more should be caught or examined.'),

		// internal error - kohana orm
		'500-200'	=> array('public' => 'Internal error', 'private' => 'generic exception during ORM interaction'),
		'500-201'	=> array('public' => 'Internal error', 'private' => 'Failed to save the ORM model'),
	)
);
