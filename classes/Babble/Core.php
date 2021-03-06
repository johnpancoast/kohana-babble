<?php defined('SYSPATH') or die('No direct script access.');

/**
 * core API functionality
 */
class Babble_Core {
	/**
	 * @var Babble Singleton instance
	 * @access protected
	 * @static
	 */
	protected static $instance = NULL;

	/**
	 * @var string The version of the request. Will default to config api.current_version value.
	 * @access private
	 * @static
	 */
	private $version = NULL;

	/**
	 * @var string Unique ID for this request.
	 * @access private
	 * @static
	 */
	private $id = NULL;

	/**
	 * @var Exception A caught exception if any.
	 * @access private
	 */
	private $exception = NULL;

	/**
	 * @var bool Was this a major exception. Typically implies that Babble cannot create an API response using normal functionality.
	 * @access private
	 */
	private $is_major_exception = FALSE;

	/**
	 * constructor initializes
	 * @access private
	 * @final
	 */
	private final function __construct()
	{
		$this->initialize();
	}

	/**
	 * destructor. will log when config api.debug set to true.
	 * @access public
	 */
	public function __destruct()
	{
		if (Kohana::$config->load('babble.debug'))
		{
			$this->create_log();
		}
	}

	/**
	 * get singleton instance
	 * @access public
	 * @static
	 * @return Babble
	 */
	public static function instance()
	{
		if ( ! isset(Babble::$instance))
		{
			Babble::$instance = new Babble;
		}

		return Babble::$instance;
	}

	/**
	 * initialize Babble
	 * @access private
	 */
	private function initialize()
	{
		// give this babble instance an id
		$this->id = sha1(uniqid($_SERVER['SERVER_ADDR'], TRUE));

		// babble version == config version by default
		$this->version = Kohana::$config->load('babble.current_version');

		$config_versions = Kohana::$config->load('babble.versions');

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
		// versions will be loaded and will handle requests/responses when they shouldn't be.
		// ...
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
			$class = isset($v['config_class']) ? $v['config_class'] : $v['class'];
			$file = $dir.str_replace('_', '/', $class).EXT;
			if (is_file($file))
			{
				$this->version = $v['version'];
				Kohana_Core_Babble::prepend_modules(array('babble-version-'.$v['version'] => $config_versions[$v['version']]));
				break;
			}
		}
	}

	/**
	 * get request version
	 * @access public
	 * @return string {@see self::$version}
	 */
	public function get_version()
	{
		return $this->version;
	}

	/**
	 * has an instance been created.
	 * @access public
	 * @static
	 * @return bbol
	 */
	public static function is_instantiated()
	{
		return isset(Babble::$instance);
	}

	/**
	 * get ID of request
	 * @access public
	 * @return string {@see self::$id}
	 */
	public function get_id()
	{
		return $this->id;
	}

	/**
	 * Store exception that was caught during normal Babble flow.
	 * @access public
	 * @param Exception $exception The exception
	 */
	public function set_exception(Exception $exception)
	{
		$this->exception = $exception;
	}

	/**
	 * Is the exception serious. Typically implies that Babble cannot create an API response using normal functionality.
	 * @access public
	 * @param bool $is Is it serious.
	 */
	public function is_major_exception($is = FALSE)
	{
		$this->is_major_exception = (bool)$is;
	}

	/**
	 * create log entry with pertinent babble info
	 * @access private
	 */
	private function create_log()
	{
		$msg = "\n";

		// if we have exception, let user know.
		if ($this->exception)
		{
			$msg .= "!!!EXCEPTION EXCEPTION EXCEPTION!!!\n".get_class($this->exception).' - '.$this->exception->getMessage()."\n";
			$msg .= "\n";
		}

		// if there was a major exception, it means we cannot log more Babble meta data
		// because we'll likely get an exception if we do (which will break stuff since this method
		// is likely called from the destructor).
		if ($this->is_major_exception)
		{
			$msg .= "*** MAJOR EXCEPTION. CANNOT LOG DEBUGGING INFO. ***\n";;
		}
		else
		{
			$request = API_Request::factory();
			$request_media = $request->media_type();
			$response = API_Response::factory();
			$response_media = $response->media_type();

			$msg .= "REQUEST:\n";
			$msg .= $request->kohana_request();

			// for formatting
			$body = $request->kohana_request()->body();
			if ( ! empty($body))
			{
				$msg .= "\n\n";
			}

			$koh_resp = $response->kohana_response();

			$msg .= "RESPONSE:\n";
			if ($koh_resp)
			{
				$msg .= $koh_resp->headers();
				$msg .= $koh_resp."\n";
			}
			$msg .= "\n";

			$msg .= "MODULE PATH:\n".str_replace(APPPATH, 'APPPATH'.DIRECTORY_SEPARATOR, Kohana_Core_Babble::get_module_path('babble-version-'.$this->get_version()))."\n";
			$msg .= "\n";

			$msg .= "REQUEST MEDIA (Content-Type):\n";
			if ($request_media)
			{
				$msg .= "  type = ".$request_media->get_media_type()."\n";
				$msg .= "  class = ".get_class($request_media)."\n";
				$msg .= "  module path = ".str_replace(APPPATH, 'APPPATH'.DIRECTORY_SEPARATOR, $request_media->get_module_path())."\n";
			}
			else
			{
				$msg .= "  no class\n";
			}
			$msg .= "\n";

			$msg .= "RESPONSE MEDIA (Accept):\n";
			if ($response_media)
			{
				$msg .= "  type = ".$response_media->get_media_type()."\n";
				$msg .= "  class = ".get_class($response_media)."\n";
				$msg .= "  module path = ".str_replace(APPPATH, 'APPPATH'.DIRECTORY_SEPARATOR, $response_media->get_module_path())."\n";
			}
			else
			{
				$msg .= "  no class\n";
			}
			$msg .= "\n";

		}

		// write log. note that if this throw an exception it will can screw things up since this method called from destructor.
		Kohana::$log->add(Log::DEBUG, "BABBLE API REQUEST\n".$this->get_id()."\n$msg")->write();
	}
}
