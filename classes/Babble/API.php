<?php defined('SYSPATH') or die('No direct script access.');

/**
 * core API functionality
 */
class Babble_API {
	private static $initialized = FALSE;

	public static function initialize()
	{
		if (self::$initialized)
		{
			return;
		}

		// if no config versions, then nothing else to load.
		$config_versions = Kohana::$config->load('api.versions');
		if (empty($config_versions))
		{
			// no need to do anything since we have no config'd version modules
			return;
		}

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
			$found = is_file($file);
			if (is_file($file))
			{
				Kohana::modules(array_merge(array('babble-app-version-'.$v['version'] => $config_versions[$v['version']]), Kohana::modules()));
				break;
			}
		}

		self::$initialized = TRUE;
	}
}
