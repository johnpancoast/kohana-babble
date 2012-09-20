<?php defined('SYSPATH') or die('No direct script access.');

/**
 * API frontend controller. all API requests should be routed here.
 * It will determine the version from the header and make an internal
 * request to appropriate controller. Note, we do no auth at this point.
 * This controller's job is just to route to a versioned API controller.
 */
class Controller_Public_API extends Controller {
	public function action_index()
	{
		// current request
		$request = Request::current();

		// we set a param to let everything know this originated as an API request
		$request->is_api_request = TRUE;

		// get the requested version from the Accept header
		// e.g., vnd.company.app-v12+xml
		// ^ looking for "12"
		$accept = API_Request::factory()->kohana_request()->headers('accept');
		if (preg_match('/^vnd\..*(\-v.*)?\+.*/', $accept, $match)) {
			$version = str_replace('-v', '', $match[1]);
		} else {
			$version = Kohana::$config->load('api.default_version');
		}
		$version = str_replace('.', '_', $version);

		// get the api controller we're calling
		$controller = $request->param('resource_controller');

		// generate internal route url
		$route_url = '/api-internal/'.$controller;
		$resource_id = $request->param('resource_id');
		if ($resource_id)
		{
			$route_url .= '/'.$resource_id;
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
		// set the main response from the API response that gets set internally.
		Request::factory($route_url, $request->param(), FALSE, $internal_routes)
			->headers($request->headers())
			->post($request->post())
			->query($request->query())
			->execute();
		$response = API_Response::factory();
		foreach ($response->get_header() as $key => $value)
		{
			$this->response->headers($key, $value);
		}
		$this->response->status($response->get_response_http_code());
		$this->response->body($response->get_response_encoded());
	}
}
