<?php defined('SYSPATH') or die('No direct script access.');

/**
 * generic API methods
 */
class Babble_API_Util {
	/**
	 * @var array header sets keyed by their relative header string.
	 * @access public
	 * @static
	 */
	public static $header_sets = array();

	public static function get_class_by_media_type($media_string = '')
	{
		$search = array('_', '+', '-v', '-', '/', '.');
		$replace = array('+', '_', '_', '_', '_', '_');
		return str_replace($search, $replace, $media_string);
	}

	/**
	 * get relevant info from accept header
	 * @access public
	 * @static
	 * @param string $header_string A header string
	 * @return mixed Array of relevant broken up header info or false upon failure.
	 * TODO I know this method can work better/faster.
	 */
	public static function get_media_type($media_string = '')
	{
		if (empty($media_string) || strpos(',', $media_string))
		{
			return FALSE;
		}

		if ( ! isset(self::$header_sets[$media_string]))
		{
			$media_string = trim($media_string);

			// Break up accept header for relevant parts.
			// e.g., accept headers can be application/json, application/hal+json, application/vnd.appname+json,
			// application/vnd.appname.behavior-v1.0+json.
			//
			// The following represent what we're pulling out (noted by brackets) from the string of
			// application/vnd.appname.behavior-v1.0+json
			//
			// header		= [application/vnd.appname.behavior-v1.0+json],
			// type			= [application]/vnd.appname.behavior-v1.0+json
			// sub_type 	= application/[vnd.appname.behavior-v1.0+json]
			// media_type	= application/vnd.appname.behavior-v1.0+[json] (e.g., application/[json], text/[html)
			// vendor_type	= application/[vnd.appname.behavior]-v1.0+json
			// version		= application/vnd.appname.behavior-v[1.0]+json
			// class		= application_vnd_appname_behavior_json
			// config_class = set in config
			$content_type = array(
				'header' => $media_string,
				'type' => NULL,
				'sub_type' => NULL,
				'media_type' => NULL,
				'vendor_type' => NULL,
				'version' => NULL,
				'class' => NULL,
				'config_class' => NULL,
			);

			// examine passed accept header.
			// probably a more efficient (maybe) regex'ish way to do all this.
			// split at /
			$split_type = explode('/', $media_string);

			// we were passed proper header string
			if (count($split_type) == 2)
			{
				// set our type and media type
				$content_type['type'] = $split_type[0];
				$content_type['sub_type'] = $split_type[1];

				// split at the +
				$split_media_type = explode('+', $content_type['sub_type']);

				// if we have a two count, then this must be a vendor media type.
				// we'll also search for a version. it automatically implies a vendor media type (for us)
				// so it will only be in this condition (no '+' in string == no version in string either)
				if (count($split_media_type) == 2)
				{
					// we have a media type
					$content_type['media_type'] = $split_media_type[1];

					// look for version
					$split_version = explode('-v', $split_media_type[0]);
					if (count($split_version) == 2)
					{
						$content_type['version'] = $split_version[1];
					}

					// custom type always 0'th element regardless of if version passed
					$content_type['vendor_type'] = $split_version[0];
				}
				// we assume if no + passed, then we weren't passed a custom_type and type (and maybe
				// version), only a type was passed to us. it's an assumption. if it fails, then it just
				// doesn't match a media type that we can return and a 406 will likely be returned.
				else
				{
					$content_type['media_type'] = $split_media_type[0];
				}
			}
			// accept header in wrong format
			else
			{
				return FALSE;
			}

			$content_type['version'] = isset($content_type['version']) ? $content_type['version'] : Kohana::$config->load('api.current_version');

			// detemine the class name based on all we've gotten
			$class = NULL;
			foreach (array('type', 'vendor_type', 'media_type') as $key)
			{
				if (isset($content_type[$key]))
				{
					$class[] = $content_type[$key];
				}
			}

			#switch (strtolower($media_string)
			// set our actual class. note that we add the version to the class name in an incorrect syntax knowing
			// that it will be fixed.
			$content_type['class'] = self::get_class_by_media_type(implode('_', $class));

			// determine if we have a default class to load for this type.
			// we check config to see if it matches our exact header string and also
			// a header string w/o the version (that may have been passed here).
			$check_types = array($media_string);
			$check_type = $content_type['type'].'/';
			if ($content_type['vendor_type'])
			{
				$check_type .= $content_type['vendor_type'];
				if ($content_type['media_type'])
				{
					$check_type .= '+'.$content_type['media_type'];
				}
			}
			elseif ($content_type['media_type'])
			{
				$check_type .= $content_type['media_type'];
			}

			$check_set = array(
				$media_string,
				$check_type
			);

			$config_types = Kohana::$config->load('api.default_media_types');

			foreach ($check_set as $v)
			{
				if (isset($config_types[$v]))
				{
					$content_type['config_class'] = 'Default_'.self::get_class_by_media_type($config_types[$v]);
				}
			}

			self::$header_sets[$media_string] = $content_type;
		}

		return self::$header_sets[$media_string];
	}

	/**
	 * return a list of accept or content-type header media types sorted by priority (where applicable)
	 * @param string $header An accept or content-type header string
	 * @return array A sorted array of media types based on their priorities (where applicable)
	 * {@see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html}
	 */
	public static function get_media_type_set($header, $class_prefix = NULL) {
		// will eventually contain sorted array of media types
		$sorted = array();

		// will eventually contain method return data
		$return = array();

		// multiple types are separated by ,
		$types = array();
		if ( ! empty($header))
		{
			$types = explode(',', $header);
		}

		if (empty($types))
		{
			return $types;
		}

		for ($i = 0, $x = 9999999, $c = count($types); $i < $c; ++$i, --$x)
		{
			$type = trim($types[$i]);

			// the params passed
			$params = explode(';', $type);

			// the type is actually the first param from above explode
			$type = array_shift($params);

			// we default this type's priority to 1.0 unless we find that a 'q' param was passed.
			$sorted[1.0 * $x] = $type;

			// loop through the params and pull out q (priority), rest are irrelevant for now
			foreach ($params as $p)
			{
				if (strpos('=', $p))
				{
					list($k, $v) = explode('=', $p);
					if ($k == 'q')
					{
						$sorted[$v * $x] = $type;
					}
				}
			}
		}

		// sort media types, break into data sets, return
		krsort($sorted);
		$bt = debug_backtrace();
		#echo $bt[1]['file'].':'.$bt[1]['line']."<br/>";
		foreach ($bt as $x)
		{
			#echo $x['file'].":".$x['line'];
		}
		#echo "<hr/>";
		foreach ($sorted as $k => $v)
		{
			$return[$v] = self::get_media_type($v);
		}
		return $return;
	}

	/**
	 * get/generate a request authentication hash key
	 * @access public
	 * @static
	 * @param string $user Public username
	 * @param string $private_key Private key
	 * @param string $url Request url
	 * @param string $method Request method
	 * @param array $post Post data
	 * @return string A hash
	 */
	public static function generate_auth_key($user, $private_key, $url, $method, $post = array()) {
		ksort($post);
		$hash_data = $user.'-'.$private_key.'-'.$method.'-'.http_build_query($post);
		return hash_hmac('sha1', $hash_data, $private_key);
	}
}
