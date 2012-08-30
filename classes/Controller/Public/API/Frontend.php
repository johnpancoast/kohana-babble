<?php defined('SYSPATH') or die('No direct script access.');

/**
 * API frontend controller. all API requests should be routed here.
 * It will determine the version from the header and make an internal
 * request to appropriate controller. Note, we do no auth at this point.
 * This controller's job is just to route to a versioned API controller.
 */
class Controller_Public_API_Frontend extends Controller {
	public function action_index()
	{
		// current request
		$request = Request::current();

		// get the requested version from the Accept header
		// e.g., vnd.company.app-v12+xml
		// ^ looking for "12"
		$accept = API_Request::factory()->request_header['Accept'];
		if (preg_match('/^vnd\..*-v(.*)\+.*/', $accept, $match)) {
			$version = $match[1];
		} else {
			$version = Kohana::$config->load('api.default_version');
		}
		$version = str_replace('.', '_', $version);

		// get the api controller we're calling
		$controller = $request->param('api_controller');

		// generate internal route url
		$route_url = '/api-internal/'.$controller;
		$api_id = $request->param('api_id');
		if ($api_id)
		{
			$route_url .= '/'.$api_id;
		}

		// create the actual kohana route that we'll pass with the internal HMVC request.
		// We do this here as opposed to creating a kohana route in the normal place
		// because this is only available internally. This is not a publicly accessible
		// route. In essence all API routes go through this frontend then to internal route.
		$route = new Route('api-internal/<controller>(/<api_id>)');
		$route->defaults(array(
			'directory' => 'Public/API/'.$version,
			'action'     => 'list',
		));
		$internal_routes = array(
			'internal-api' => $route
		);

		// make internal hmvc call to controller.
		// set the main response from the response we get.
		try
		{
			$internal_response = Request::factory($route_url, $request->param(), FALSE, $internal_routes)
				// note that we created a custom request client to override kohana's. this is so we can handle 404's in a API'ish way.
				->client(new Request_Client_Internal_API($request->param()))
				->headers($request->headers())
				->post($request->post())
				->query($request->query())
				->execute();
			$this->response->body($internal_response);
		}
		catch (HTTP_Exception $e)
		{
			// try catch the API exception so normal logging occurs
			try
			{
				throw new API_Response_Exception('not found', '404-000');
			}
			catch (API_Response_Exception $e)
			{
				$this->response->body(API_Response::factory()->set_response($e->get_response_code())->get_encoded_response());
			}
		}
		catch (Exception $e)
		{
			// try catch the API exception so normal logging occurs
			try
			{
				throw new API_Response_Exception('something aint right', '500-000');
			}
			catch (API_Response_Exception $e)
			{
				$this->response->body(API_Response::factory()->set_response($e->get_response_code())->get_encode_response());
			}
		}
	}
}
