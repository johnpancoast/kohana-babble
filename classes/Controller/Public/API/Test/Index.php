<?php defined('SYSPATH') or die('No direct script access.');

/**
 * FIXME
 * !!This class should be changed to Kohana's request class!!
 *
 * FIXME
 * This class is close to being removed completely since there are available
 * clients to use. I particularly like 
 * This code is for testing the API. It is only accessible by kohana route when in debug environment
 * (i.e., Kohana::DEVELOPMENT).
 *`
 * You should have an understanding of how the API works before using this.
 *
 * This takes a request and maps it to an actual API request a client would make. Essentially this controller
 * is a client that allows you to dynamically change its calls by changing the URI and query string params
 * you pass to it.
 *
 * Note that this controller is not an API nor intended to be. It just takes your parameters, creates an actual
 * API call (via cURL), and gives you returned data with helpful details. It's like a proxy to the API.
 *
 * Example use:
 * http://example.com/testapi/[resource_controller]/[request_method]/[resource_id]?querystring
 *
 * resource_controller = the resource controller to call in API call
 * request_method = HTTP request method that we pass in API call. one of get|put|post|delete.
 * resource_id = the resource id. not required.
 * querystring = the query string. not required. see below.
 *
 * Query string data is passed onto the actual API call as-is w/ the exception of data intended to be POSTed
 * to the API call. Normal API clients POST resource data in an array called "resource_data". You can POST
 * resource_data to the API call by passing your test data in a query string array called "rd". All data in
 * this query string param will be POSTed to the API in a variable called resource_data.
 *
 * This example would POST a name and email to the "user" resource controller.
 * http://example.com/testapi/user/post?rd[name]=username&rd[email]=email
 *
 * This example would PUT name and email to a resource_id of "mytitle".
 * http://example.com/testapi/user/put/mytitle?rd[name]=username&rd[email]=email
 *
 * This would pass a GET to the books resource and pass it an offset and limit parameter.
 * http://example.com/testapi/book/get?offset=30&limit=10
 *
 * This would pass a DELETE of book 18
 * http://example.com/testapi/book/delete/18
 */

class Controller_Public_API_Test_Index extends Controller {
	private function callApi($url, $method = 'GET', $post = array(), $header = array())
	{
		// pull config values
		$config = Kohana::$config->load('babble.testing');
		if (empty($config['user']) || empty($config['pass']))
		{
			throw new Exception('must set test api user and password');
		}

		// validate method
		$method = strtoupper($method);
		if ( ! in_array($method, array('GET', 'PUT', 'POST', 'DELETE'))) {
			throw new Exception('invalid request method passed');
		}

		// setup passed content
		$resource_post = array();
		if ($method == 'POST' || $method == 'PUT')
		{
			ksort($post);
			$resource_post = $post;
		}
		#$header[] = 'Content-type: application/json';
		#$content = json_encode($resource_post);
		$content = http_build_query($resource_post);

		// setup header
		$header = array_merge($config['header'], $header);

		// add auth header.
		// this header and {@see API_Util::generate_auth_key()} is a good hint into how the API handles authentication.
		$header[] = 'Authorization: '.$config['user'].':'.API_Util::generate_auth_key($config['user'], $config['pass'], $url, $method, $resource_post);

		// call api
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
		if ($method == 'POST' || $method == 'PUT')
		{
			curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
		}
		if ($config['curl_debug_file'])
		{
			curl_setopt($ch, CURLOPT_STDERR, fopen($config['curl_debug_file'], 'w+'));
		}

		$curl_resp = curl_exec($ch);
		$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		return array('status' => $http_status, 'response' => $curl_resp);
	}

	public function action_index()
	{
		// pull out passed params
		$uri = $this->request->uri();
		if (preg_match('|^testapi/(\w+)/?(\w+)?/?(\d+)?|', $uri, $match))
		{
			if ( ! isset($match[1]) || ! isset($match[2]))
			{
				throw new Exception('Incorrect usage of testapi controller');
			}
			$resource = $match[1];
			$method = strtoupper($match[2]);
			if (isset($match[3]))
			{
				$resource_id = $match[3];
			}
		}

		// create proper POST and query string data.
		$query = $this->request->query();
		$post = array();
		if (isset($query['rd']))
		{
			$post = $query['rd'];
			unset($query['rd']);
		}

		// generate API url then call API
		$url = URL::base(TRUE, (isset($_SERVER['HTTPS']) ? 'https' : 'http')).'api/'.$resource;
		if ( ! empty($resource_id))
		{
			$url .= '/'.$resource_id;
		}
		if ( ! empty($query))
		{
			$url .= '?'.http_build_query($query);
		}

		$resp = $this->callApi($url, $method, $post);
		echo $resp['response'];
	}
}
