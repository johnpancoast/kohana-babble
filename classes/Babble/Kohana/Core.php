<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Extends Kohana core functionality so we can add/remove modules without having
 * to reload all of them. Since Babble requires setting modules dynamically and
 * may require this often due to how content negotiation is handled, it can be a
 * performance hit to have to frequently reload _all_ modules. Instead we can
 * prepend/append modules using this class.
 */
class Babble_Kohana_Core extends Kohana {
	public static function add_modules(array $modules = array(), $append = TRUE)
	{
		// new modules we'll build
		$new_modules = array();

		// Start a new list of include paths, APPPATH first
		$paths = array(APPPATH);

		if ($append)
		{
			foreach (Kohana::$_modules as $name => $path)
			{
				$paths[] = $new_modules[$name] = $path;
			}
			foreach ($modules as $name => $path)
			{
				if (is_dir($path))
				{
					// Add the module to include paths
					$paths[] = $new_modules[$name] = realpath($path).DIRECTORY_SEPARATOR;
				}
				else
				{
					// This module is invalid, remove it
					throw new Kohana_Exception('Attempted to load an invalid or missing module \':module\' at \':path\'', array(
						':module' => $name,
						':path'   => Debug::path($path),
					));
				}
			}
		}
		else
		{
			foreach ($modules as $name => $path)
			{
				if (is_dir($path))
				{
					// Add the module to include paths
					$paths[] = $new_modules[$name] = realpath($path).DIRECTORY_SEPARATOR;
				}
				else
				{
					// This module is invalid, remove it
					throw new Kohana_Exception('Attempted to load an invalid or missing module \':module\' at \':path\'', array(
						':module' => $name,
						':path'   => Debug::path($path),
					));
				}
			}
			foreach (Kohana::$_modules as $name => $path)
			{
				$paths[] = $new_modules[$name] = $path;
			}
		}

		// Finish the include paths by adding SYSPATH
		$paths[] = SYSPATH;

		// Set the new include paths
		Kohana::$_paths = $paths;

		// Set the current module list
		Kohana::$_modules = $new_modules;

		foreach ($modules as $path)
		{
			$init = $path.'init'.EXT;

			if (is_file($init))
			{
				// Include the module initialization file once
				require_once $init;
			}
		}

		return Kohana::$_modules;
	}

	public static function prepend_modules(array $modules = array())
	{
		return self::add_modules($modules, FALSE);
	}

	public static function append_modules(array $modules = array())
	{
		return self::add_modules($modules, TRUE);
	}

	public static function remove_modules($keys = array())
	{
		if (is_string($keys))
		{
			$keys = array($keys);
		}
		foreach ($keys as $k)
		{
			if ( ! isset(Kohana::$_modules[$k]))
			{
				continue;
			}

			$path = Kohana::$_modules[$k];
			$match = array_search($path, Kohana::$_paths);

			if ( ! is_int($match))
			{
				continue;
			}

			unset(Kohana::$_modules[$k]);
			unset(Kohana::$_paths[$match]);
		}
	}

	public static function get_module_path($key)
	{
		return Kohana::$_modules[$key];
	}
}
