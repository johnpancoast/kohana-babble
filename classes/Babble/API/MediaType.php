<?php defined('SYSPATH') or die('No direct script access.');

/**
 * api media type
 * @abstract
 */
abstract class Babble_API_MediaType {
	/**
	 * @var string The media type. This should be a valid media type as described in
	 * {@link http://www.iana.org/assignments/media-types/index.html}. Of course vendor
	 * types are allowed as well.
	 * @access protected
	 */
	protected $media_type = NULL;

	private $module_path = NULL;

	/**
	 * get encoded data from an array
	 * @abstract
	 * @access protected
	 * @param mixed $data The data to encode
	 * @return string Expected to return an encoded string
	 */
	abstract protected function _get_data_encoded($data = array());

	/**
	 * get decoded array from an encoded string.
	 * @abstract
	 * @access protected
	 * @param string $data The data to decode
	 * @return array Expected to return a decoded array
	 */
	abstract protected function _get_data_decoded($data = NULL);

	/**
	 * constructor. children cannot be instantiated directly.
	 */
	final protected function __construct() {}

	/**
	 * factory method to load a media type class
	 * @param string $header A media type header which can be either Content-type or Accept
	 * @param bool $is_accept_header If this is accept header. If false, it implies it's content type.
	 * @return API_MediaType A child of it.
	 */
	public static function factory($header, $is_accept_header = TRUE)
	{
		// media types from header
		$media_types = API_Util::get_media_type_set($header);

		// possible versions
		$config_versions = Kohana::$config->load('api.versions');

		// babble version
		$bab_version = Babble_API::get_version();
		$bab_version_path = Kohana_Core_Babble::get_module_path('babble-version-'.$bab_version);

		// attempt to find a matching class
		$class = NULL;
		foreach ($media_types as $header => $type)
		{
			// whether or not a config value was set to match on this media type
			$config_type_found = (isset($config_type_found) && $config_type_found) ? $config_type_found : ( ! is_null($type['config_class']));

			// attempt loading class that matches request. if nothing, attempt loading class that matches config media type
			// if there was a matching config media type set for this header.
			$arr = array('API_MediaType_Driver_'.$type['class']);
			if (isset($type['config_class']))
			{
				$arr[] = 'API_MediaType_Driver_'.$type['config_class'];
			}
			foreach ($arr AS $class_name)
			{
				// if we're attemping a version other than the version module we've loaded for the request, load this
				// attempted version as a tmp module. we'll remove the loaded version module since it may be unrelated
				// to the version of this media type we're attempting to load.
				if ($type['version'] != $bab_version)
				{
					// we will temporarily remove the loaded version module
					Kohana_Core_Babble::remove_modules('babble-version-'.$bab_version);

					if (isset($config_versions[$type['version']]))
					{
						// ignore caught exception if related to non-existent dir. we just won't load it.
						try
						{
							Kohana_Core_Babble::prepend_modules(array('babble-version-tmp-'.$type['version'] => $config_versions[$type['version']]));
						}
						catch (Kohana_Exception $e)
						{
							// ignore if it was invalid dir, throw exception otherwise.
							if (strpos($e->getMessage(), 'Attempted to load an invalid or missing module') === FALSE)
							{
								throw $e;
							}
						}
					}
				}

				if (class_exists($class_name))
				{
					$class = new $class_name;

					if (isset($type))
					{
						$class->set_media_type_set($type);
					}

					// set meta data since we found something.
					$class->set_module_path($type['version'] == $bab_version ? $bab_version_path : $config_version[$type['version']]);

					break 2;
				}

				// set original kohana modules
				if ($type['version'] != $bab_version)
				{
					Kohana_Core_Babble::remove_modules('babble-version-tmp-'.$type['version']);

					// ignore caught exception if related to non-existent dir. we just won't load it.
					try
					{
						Kohana_Core_Babble::prepend_modules(array('babble-version-'.$bab_version => $bab_version_path));
					}
					catch (Kohana_Exception $e)
					{
						// ignore if it was invalid dir, throw exception otherwise.
						if (strpos($e->getMessage(), 'Attempted to load an invalid or missing module') === FALSE)
						{
							throw $e;
						}
					}
				}
			}
		}

		// if we found no class, then we have nothing to respond with.
		if ( ! $class)
		{
			// dev set a config type that doesn't exist
			if (isset($config_type_found) && $config_type_found)
			{
				throw new API_MediaType_Exception_NoConfigClass;
			}
			else
			{
				$class = new API_MediaType_DriverDefault;
			}
		}

		// must extend media type
		if ( ! ($class instanceof API_MediaType))
		{
			throw new API_MediaType_Exception_Inheritance;
		}

		return $class;
	}

	private function set_media_type_set($type_set)
	{
		$this->media_type_set = $type_set;
	}

	public function get_media_type_set()
	{
		return $this->media_type_set;
	}

	/**
	 * public interface to get encoded data from an array
	 * @access public
	 * @param mixed $data The data to encode
	 * @return string Expected to return an encoded string
	 */
	public function get_data_encoded($data = NULL)
	{
		if ( ! $data)
		{
			return '';
		}
		return $this->_get_data_encoded($data);
	}

	/**
	 * public interface to get decoded array from an encoded string.
	 * @access public
	 * @param string $data The data to decode
	 * @return array Expected to return a decoded array
	 */
	public function get_data_decoded($data = NULL)
	{
		if ( ! $data)
		{
			return array();
		}
		return $this->_get_data_decoded($data);
	}

	/**
	 * get media type
	 * @return string
	 */
	public function get_media_type()
	{
		return $this->media_type;
	}

	/**
	 * set module path at the time this class was loaded.
	 * @access protected
	 * @param string $path The module path.
	 */
	protected function set_module_path($path)
	{
		$this->module_path = $path;
	}
}
