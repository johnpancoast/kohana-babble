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
			if (API_Request::factory()->kohana_request()->is_api_request)
			{
				try
				{
					$api_response = API_Response::factory();

					// add headers
					foreach ($api_response->get_header() as $key => $value)
					{
						$response->headers($key, $value);
					}

					// Set the response body
					$code = ($e instanceof API_Response_Exception) ? $e->get_response_code() : $http_status.'-000';
					$api_response->set_response($code);
					$response->body($api_response->get_body_encoded());
				}
				// if we get an exception back while attempting to set a response we must just pass the header and respond
				// with generic body. This generally occurs for unimplemented request or response methods (i.e., client requested xml
				// when we don't have that available) but other cases may be possible as well.
				catch (Exception $e)
				{
					// determine http code to pass
					if ($e instanceof API_Response_Exception)
					{
						$ecode = $e->get_response_http_code();
						$http_code = $ecode ? $ecode : '500';
						$message = Kohana::message('api', 'responses.'.$e->get_response_code().'.public');
					}
					else
					{
						$http_code = '500';
						$message = $e->getMessage();
					}
					$response->status($http_code);
					$response->headers('Content-Type', 'text/plain');
					$response->body($message);
				}
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
			$response->status(500);
			$response->headers('Content-Type', 'text/plain');
			$response->body(Kohana_Exception::text($e));
		}

		return $response;
	}
} // End Kohana_Exception
