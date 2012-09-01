<?php defined('SYSPATH') OR die('No direct access');
/**
 * Kohana exception class. This transparently extends system/classes/Kohana/Exception
 * so we can handle case for API response
 */
class Kohana_Exception extends Kohana_Kohana_Exception {
	/**
	 * Get a Response object representing the exception
	 * {@see parent::response()}
	 */
	public static function response(Exception $e)
	{
		try
		{
			// Get the exception information
			$class   = get_class($e);
			$code    = $e->getCode();
			$message = $e->getMessage();
			$file    = $e->getFile();
			$line    = $e->getLine();
			$trace   = $e->getTrace();

			if ( ! headers_sent())
			{
				// Make sure the proper http header is sent
				$http_header_status = ($e instanceof HTTP_Exception) ? $code : 500;
			}

			/**
			 * HTTP_Exceptions are constructed in the HTTP_Exception::factory()
			 * method. We need to remove that entry from the trace and overwrite
			 * the variables from above.
			 */
			if ($e instanceof HTTP_Exception AND $trace[0]['function'] == 'factory')
			{
				extract(array_shift($trace));
			}


			if ($e instanceof ErrorException)
			{
				/**
				 * If XDebug is installed, and this is a fatal error,
				 * use XDebug to generate the stack trace
				 */
				if (function_exists('xdebug_get_function_stack') AND $code == E_ERROR)
				{
					$trace = array_slice(array_reverse(xdebug_get_function_stack()), 4);

					foreach ($trace as & $frame)
					{
						/**
						 * XDebug pre 2.1.1 doesn't currently set the call type key
						 * http://bugs.xdebug.org/view.php?id=695
						 */
						if ( ! isset($frame['type']))
						{
							$frame['type'] = '??';
						}

						// XDebug also has a different name for the parameters array
						if (isset($frame['params']) AND ! isset($frame['args']))
						{
							$frame['args'] = $frame['params'];
						}
					}
				}
				
				if (isset(Kohana_Exception::$php_errors[$code]))
				{
					// Use the human-readable error name
					$code = Kohana_Exception::$php_errors[$code];
				}
			}

			/**
			 * The stack trace becomes unmanageable inside PHPUnit.
			 *
			 * The error view ends up several GB in size, taking
			 * serveral minutes to render.
			 */
			if (defined('PHPUnit_MAIN_METHOD'))
			{
				$trace = array_slice($trace, 0, 2);
			}

			// Prepare the response object.
			$response = Response::factory();

			// response status
			$http_status = ($e instanceof HTTP_Exception) ? $e->getCode() : 500;

			$response->status($http_status);

			// response for API requests
			if (Request::initial()->is_api_request)
			{
				// FIXME add header from API_Response
				// <here>

				// Set the response body
				$code = ($e instanceof API_Response_Exception) ? $e->get_response_code() : $http_status.'-000';
				$response->body(API_Response::factory()->set_response($code)->get_encoded_response());
			}
			// standard response
			else
			{
				// Set the response headers
				$response->headers('Content-Type', Kohana_Exception::$error_view_content_type.'; charset='.Kohana::$charset);

				// Instantiate the error view.
				$view = View::factory(Kohana_Exception::$error_view, get_defined_vars());

				// Set the response body
				$response->body($view->render());
			}
		}
		catch (Exception $e)
		{
			/**
			 * Things are going badly for us, Lets try to keep things under control by
			 * generating a simpler response object.
			 */
			$response = Response::factory();

			// Added case for API access
			if (Request::initial()->is_api_request)
			{
				$response->status(500);
				// FIXME add header from API_Response
				// <here>
				$response->body(API_Response::factory()->set_response('500-000')->get_encoded_response());
			}
			else
			{
				$response->status(500);
				$response->headers('Content-Type', 'text/plain');
				$response->body(Kohana_Exception::text($e));
			}
		}

		return $response;
	}
} // End Kohana_Exception
