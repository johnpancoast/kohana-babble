<?php defined('SYSPATH') or die('No direct script access.');

/**
 * api meta data primarily used for logging
 */
class Babble_API_Meta {
	private static $request = NULL;
	private static $response = NULL;
	private static $header_content_type = array();
	private static $header_accept = array();
	private static $version_module_directory = NULL;

	public static function set_request(API_Request $request)
	{
		Babble_API::$request = $request;
	}
	public static function set_response(API_Response $response)
	{
		Babble_API::$response = $response;
	}
	public static function set_header_content_type($media_type, $module_directory)
	{
		Babble_API::$header_content_type = array('media_type' => $media_type, 'module_directory' => $module_directory);
	}
	public static function set_header_accept($media_type, $module_directory)
	{
		Babble_API::$header_accept = array('media_type' => $media_type, 'module_directory' => $module_directory);
	}
	public static function set_version_module_directory($version_module_directory)
	{
		Babble_API::$version_module_directory = $version_module_directory;
	}
}
