<?php defined('SYSPATH') or die('No direct script access.');

/**
 * core API functionality
 */
class Babble_API {
	private static $initialized = FALSE;
	private static $version = NULL;
	private static $id = NULL;

	public static function initialize()
	{
		if (Babble_API::$initialized)
		{
			return;
		}

		// we set this at the beginning to let everything following this know that we're in an API request.
		Babble_API::$initialized = TRUE;

		// give this babble instance an id
		Babble_API::$id = uniqid($_SERVER['SERVER_ADDR'], TRUE);

		$config_versions = Kohana::$config->load('api.versions');

		// decide which passed version to use based off of the request method.
		$write = in_array($_SERVER['REQUEST_METHOD'], array('POST', 'PUT', 'PATCH'));

		// set type we're workin with
		$type = $write
			? API_Util::get_media_type_set(Request::current()->headers('content-type'))
			: API_Util::get_media_type_set(Request::current()->headers('accept'));

		// manually try to see if we get a matched media type class. if we get a match, then it's a safe module to load.
		// we _must_ do this check and _cannot_ just load each version as a kohana module until after this check. this is because
		// 1.) it's less files to check once we load worthy modules  -and- 2.) if we load all modules (or even modules for passed versions)
		// before knowing that they have the right media class to handle the request/response, then media type classes among different
		// versions will be loaded and handling requests/responses when they shouldn't be.
		// get all passed versions
		foreach ($type as $k => $v)
		{
			if ( ! isset($config_versions[$v['version']]))
			{
				continue;
			}
			$dir = $config_versions[$v['version']].DIRECTORY_SEPARATOR
			. 'classes'.DIRECTORY_SEPARATOR
			. 'API'.DIRECTORY_SEPARATOR
			. 'MediaType'.DIRECTORY_SEPARATOR
			. 'Driver'.DIRECTORY_SEPARATOR;
			$file = $dir.str_replace('_', '/', $v['class']).EXT;
			if (is_file($file))
			{
				Babble_API::$version = $v['version'];
				Kohana_Core_Babble::prepend_modules(array('babble-version-'.$v['version'] => $config_versions[$v['version']]));
				API_Meta::set_version_module_directory($config_versions[$v['version']]);
				break;
			}
		}

		// now that our version module is loaded properly, set meta data on request.
		API_Meta::set_request(API_Request::factory());
		API_Meta::set_response(API_Response::factory());
	}

	public static function get_version()
	{
		return Babble_API::$version;
	}

	public static function is_initialized()
	{
		return Babble_API::$initialized;
	}

	public static function get_id()
	{
		return Babble_API::$id;
	}
}
