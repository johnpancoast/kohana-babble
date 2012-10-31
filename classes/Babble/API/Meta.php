<?php defined('SYSPATH') or die('No direct script access.');

/**
 * api meta data primarily used for logging
 * TODO - a lot of this may not stay... TBD
 */
class Babble_API_Meta {
	protected static $request = NULL;
	protected static $response = NULL;
	protected static $header_content_type = array();
	protected static $header_accept = array();
	protected static $version_module_directory = NULL;

	public static function set_request(Babble_API_Request $request)
	{
		API_Meta::$request = $request;
	}

	public static function set_response(Babble_API_Response $response)
	{
		API_Meta::$response = $response;
	}

	public static function set_header_content_type($media_type, $module_directory)
	{
		API_Meta::$header_content_type = array('media_type' => $media_type, 'module_directory' => $module_directory);
	}

	public static function set_header_accept($media_type, $module_directory)
	{
		API_Meta::$header_accept = array('media_type' => $media_type, 'module_directory' => $module_directory);
	}

	public static function set_version_module_directory($version_module_directory)
	{
		API_Meta::$version_module_directory = $version_module_directory;
	}
}
