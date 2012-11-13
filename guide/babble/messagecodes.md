# Message Codes
A Babble message code consists of 2 parts separated by a -. The first part is
an [HTTP Status Code](http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html).
The second part is an internal code. Codes are defined in `messages/babble.php`.
In the messages file the key is the message code and the value is an array
containing messages of these types.

- public (required) - The public message printed to user. Generally
  corresponds to an HTTP message. This message is printed in the response.
- public_hint (optional) - A more detailed public message. This message can
  be printed in the response.
- private (optional) - An internal message for logging purposes. This _is
  not_ printed in the response.

At present, these are our current message codes.
~~~
<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * api messages
 */
return array
(
	/**
	 * API response messages.
	 *
	 * a response message has a code as it's key and an assoc array of public and optional
	 * public_hint and/or private messages. public are what the client will be shown. public_hint
	 * are unimplemented but may be used soon and private are mostly for our reading and perhaps
	 * logging in the future.
	 *
	 * keys are http status codes followed by a "-" followed by an internal identifier.
	 * The internal identifiers should be broken into logical application sections where applicable.
	 *
	 * For info on http status codes see {@see http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html}
	 *
	 * TODO - add ability for substitution from developer's messages
	 */
	'responses' => array(
		#########################################
		## INFORMATIONAL (http codes in 100's)
		#########################################
		'100-000'		=> array('public' => 'Continue'),
		'101-000'		=> array('public' => 'Switching Protocols'),

		#########################################
		## SUCCESSFUL (http codes in 200's)
		#########################################
		// success
		'200-000'		=> array('public' => 'Ok'),

		// created
		'201-000'		=> array('public' => 'Created'),

		// others
		'202-000'		=> array('public' => 'Accepted'),
		'203-000'		=> array('public' => 'Non-Authoritative Information'),
		'204-000'		=> array('public' => 'No Content'),
		'205-000'		=> array('public' => 'Reset Content'),
		'206-000'		=> array('public' => 'Partial Content'),

		#########################################
		## REDIRECTION (http codes in 300's)
		#########################################
		'300-000'		=> array('public' => 'Multiple Choices'),
		'301-000'		=> array('public' => 'Moved Permanently'),
		'302-000'		=> array('public' => 'Found'),
		'303-000'		=> array('public' => 'See Other'),
		'304-000'		=> array('public' => 'Not Modified'),
		'305-000'		=> array('public' => 'Use Proxy'),
		'306-000'		=> array('public' => '(Unused)'),
		'307-000'		=> array('public' => 'Temporary Redirect'),

		#########################################
		## USER ERRORS (http codes in 400's)
		#########################################
		// bad request - database
		'400-000'		=> array('public' => 'Bad Request', 'public_hint' => 'Duplicate field', 'private' => 'Duplicate DB Key'),

		// bad request - orm
		'400-100'		=> array('public' => 'Bad Request', 'public_hint' => 'Invalid data', 'private' => 'ORM validation failed'),
		'400-101'		=> array('public' => 'Bad Request', 'public_hint' => 'Non-existent field passed', 'private' => 'The client attempted to set a value of a non-existen model field'),

		// bad request - media type & request/response
		'400-200'		=> array('public' => 'Bad Request', 'public_hint' => 'Invalid data passed', 'private' => 'Media type class could not understand data sent in request.'),

		// unauthorized
		'401-000'		=> array('public' => 'Unauthorized', 'private' => 'Access denied due to failed authentication'),
		'401-001'		=> array('public' => 'Unauthorized', 'private' => 'Access denied due to failed access'),

		// payment required
		'402-000'		=> array('public' => 'Payment Required'),

		// forbidden
		'403-000'		=> array('public' => 'Forbidden'),

		// not found
		'404-000'		=> array('public' => 'Not Found', 'private' => 'The requested URL was not found'),

		// request method not allowed
		'405-000'		=> array('public' => 'Method Not Allowed'),

		// not acceptable. cannot respond with content in format of 'Accept' header. 
		'406-000'		=> array('public' => 'Not Acceptable', 'private' => 'Generic 406'),
		'406-001'		=> array('public' => 'Not Acceptable', 'private' => 'No media type class functionality.'),
		'406-002'		=> array('public' => 'Not Acceptable', 'private' => 'Developer set non-existent config media type class.'),

		// others
		'407-000'		=> array('public' => 'Proxy Authentication Required'),
		'409-000'		=> array('public' => 'Conflict'),
		'410-000'		=> array('public' => 'Gone'),
		'411-000'		=> array('public' => 'Length Required'),
		'412-000'		=> array('public' => 'Precondition Failed'),
		'413-000'		=> array('public' => 'Request Entity Too Large'),
		'414-000'		=> array('public' => 'Request-URI Too Long'),

		// unsupported media type
		'415-000'		=> array('public' => 'Unsupported Media Type'),
		'415-001'		=> array('public' => 'Unsupported Media Type', 'private' => 'No media type class functionality.'),
		'415-002'		=> array('public' => 'Unsupported Media Type', 'private' => 'Developer set non-existent config media type class'),

		// others
		'416-000'		=> array('public' => 'Requested Range Not Satisfiable'),
		'417-000'		=> array('public' => 'Expectation Failed'),

		#########################################
		## SYSTEM ERRORS (http codes in 500's)
		#########################################
		// internal error - generic
		'500-000'		=> array('public' => 'Internal Server Error', 'private' => 'A terrible error occured'),
		'500-001'		=> array('public' => 'Internal Server Error', 'private' => 'Unknown API response code'),
		'500-002'		=> array('public' => 'Internal Server Error', 'private' => 'No API response'),
		'500-003'		=> array('public' => 'Internal Server Error', 'private' => 'Developer set a non-existent default api response driver.'),
		'500-004'		=> array('public' => 'Internal Server Error', 'private' => 'Problem with media type class(es).'),

		// internal error - database
		'500-100'		=> array('public' => 'Internal Server Error', 'private' => 'A generic database exception. Perhaps more should be caught or examined.'),

		// internal error - kohana orm
		'500-200'		=> array('public' => 'Internal Server Error', 'private' => 'Generic exception during ORM interaction'),
		'500-201'		=> array('public' => 'Internal Server Error', 'private' => 'Failed to save or delete an ORM model object'),

		// others
		'501-000'		=> array('public' => 'Not Implemented'),
		'502-000'		=> array('public' => 'Bad Gateway'),
		'503-000'		=> array('public' => 'Service Unavailable'),
		'504-000'		=> array('public' => 'Gateway Timeout'),
		'505-000'		=> array('public' => 'HTTP Version Not Supported'),
	)
);
~~~
