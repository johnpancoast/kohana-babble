<?php defined('SYSPATH') or die('No direct script access.');

/**
 * API frontend controller. all API requests should be routed here.
 * It will determine the version from the header and make an internal
 * request to appropriate controller. Note, we do no auth at this point.
 * This controller's job is just to route to a versioned API controller.
 */
class Babble_Controller_Public_APIFrontend extends Controller {
	public function before()
	{
		Babble_API::initialize();
		parent::before();
	}

	public function action_index()
	{
		// current request
		$request = Request::current();

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
		$route = new Route('api-internal/<controller>(/<resource_id>)');
		$route->defaults(array(
			'directory' => 'Public/API/',
			'action'     => 'list',
		));
		$internal_routes = array(
			'internal-api' => $route
		);

		// make internal hmvc call to controller.
		$internal_request  = Request::factory($route_url, $request->param(), FALSE, $internal_routes)
			->headers($request->headers())
			->post($request->post())
			->query($request->query())
			->execute();

		// set the main response from the API response that gets set internally.
		$this->response->status($internal_request->status());
		foreach ($internal_request->headers() as $k => $v)
		{
			$this->response->headers($k, $v);
		}
		$this->response->body($internal_request->body());
	}
}
