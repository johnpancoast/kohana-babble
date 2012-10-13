<?php defined('SYSPATH') or die('No direct script access.');

/**
 * api media type
 * @abstract
 */
abstract class API_MediaType {
	/**
	 * @var string The media type. This should be a valid media type as described in
	 * {@link http://www.iana.org/assignments/media-types/index.html}. Of course vendor
	 * types are allowed as well.
	 * @access protected
	 */
	protected $media_type = NULL;

	/**
	 * get encoded data from an array
	 * @abstract
	 * @access public
	 * @param array $data The data to encode
	 * @return string Expected to return an encoded string
	 */
	abstract public function get_data_encoded(array $data = array());

	/**
	 * get decoded array from an encoded string.
	 * @abstract
	 * @access public
	 * @param string $data The data to decode
	 * @return array Expected to return a decoded array
	 */
	abstract public function get_data_decoded($data = NULL);

	/**
	 * constructor. children cannot be instantiated directly.
	 */
	final protected function __construct() {}

	/**
	 * factory method to load a media type class
	 * @param string $header A media type header which can be either Content-type or Accept
	 * @return API_MediaType A child of it.
	 */
	public static function factory($header)
	{
		// media types from header
		$media_types = API_Util::get_media_type_set($header);

		if (empty($header) && empty($media_types))
		{
			return array();
		}

		foreach ($media_types as $type)
		{
			if (class_exists('API_MediaType_'.$type['real']['class']))
			{
				$class_name = 'API_MediaType_'.$type['real']['class'];
				break;
			}
			elseif (class_exists('API_MediaType_'.$type['real']['config_class']))
			{
				$class_name = 'API_MediaType_'.$type['real']['config_class'];
				break;
			}
			elseif (class_exists('API_MediaType_'.$type['real']['default_class']))
			{
				$class_name = 'API_MediaType_'.$type['real']['default_class'];
				break;
			}

			$config_type_found = (isset($config_type_found) && $config_type_found) ? $config_type_found : ( ! is_null($type['real']['config_class']));
		}

		// if we found no class, then we have nothing to respond with.
		if ( ! isset($class_name))
		{
			// dev set a config type that doesn't exist
			if (isset($config_type_found) && $config_type_found)
			{
				throw new API_MediaType_Exception_NoConfigClass;
			}
			else
			{
				throw new API_MediaType_Exception_NoClass;
			}
		}

		$class = new $class_name;
		if ( ! ($class instanceof API_MediaType))
		{
			throw new API_MediaType_Exception_Inheritance;
		}

		return $class;
	}

	/**
	 * get media type
	 * @return string
	 */
	public function get_media_type()
	{
		return $this->media_type;
	}
}
