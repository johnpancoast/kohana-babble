<?php defined('SYSPATH') or die('No direct script access.');

/**
 * kohana orm specific api/model interaction class.
 *
 * Note that due to a limitation in how Kohana's ORM works we cannot pass
 * {@see self::$model_fields} to the selects for the ORM query. Meaning, we
 * cannot limit the returned dataset to a selection of certain columns in
 * the query itself. The only workaround is to loop the result set and
 * manually set the fields to be returned. For most things this _should_
 * be ok but for get_list() you'll get a bit of a performance hit depending
 * on the amount of results you're returning.
 */
class Babble_API_Model_ORM extends API_Model {
	/**
	 * remove model fields we're not instructed to return
	 * @access private
	 * @param array $object the object we're editing
	 * @return array a refined object
	 */
	private function remove_model_fields(array $object = array())
	{
		if (empty($this->model_fields))
		{
			return $object;
		}

		$return = array();
		foreach ($this->model_fields as $k)
		{
			$return[$k] = $object[$k];
		}
		return $return;
	}

	/**
	 * for a set of objects, remove the model fields we're not instructed to return
	 * @access private
	 * @param array $object_set the list of objects we're editing
	 * @return array a refined set of objects
	 */
	private function remove_model_field_set(array $object_set = array())
	{
		$return = array();
		for ($i = 0, $c = count($object_list); $i < $c; ++$i)
		{
			$row = $object_list[$i];
			$return[] = $this->remove_model_fields($row);
			unset($object_list[$i]);
		}
		return $return;
	}

	/**
	 * @see parent::get_list();
	 * TODO - this method is incomplete. needs support for limit, sorting, and searching
	 */
	public function get_list($page = 1, $limit = NULL, $sort = NULL, $search = NULL)
	{
		// Be sure to only profile if it's enabled
		/*
		if (Kohana::$profiling === TRUE)
		{
			// Start a new benchmark
			$benchmark = Profiler::start('Your Category', __FUNCTION__);
		}
		*/

		$limit = $limit ? $limit : 100;
		$offset = $page ? ($page-1) * $limit : 0;

		// catch all exceptions
		try
		{
			$objs = ORM::factory($this->model)
				->limit($limit)
				->offset($offset);
			$result = $objs->find_all();
			$resp = new Babble_API_Resource_Collection;;
			foreach ($result as $row)
			{
				$resp[] = $resp->append($this->remove_model_fields($row->as_array()));
			}

			return $resp;
		}
		catch (API_Model_Exception $e)
		{
			// just rethrow API_Model_Exception's
			throw $e;
		}
		catch (ORM_Validation_Exception $e)
		{
			throw new API_Model_Exception($e->getMessage(), '400-100');
		}
		catch (Database_Exception $e)
		{
			$message = $e->getMessage();
			throw new API_Model_Exception($message, '500-100');
		}
		catch (Exception $e)
		{
			// for some reason, kohana decided to throw a general exception if you set a non-existent field.
			// wish they threw validation exception but whatever.
			$message = $e->getMessage();
			throw new API_Model_Exception($message, '500-200');
		}

		/*
		if (isset($benchmark))
		{
			// Stop the benchmark
			Profiler::stop($benchmark);
		}
		echo View::factory('profiler/stats');
		*/
	}

	/**
	 * @see parent::get();
	 */
	public function get($object_id)
	{
		// catch all exceptions
		try
		{
			$obj = ORM::factory($this->model, $object_id);
			if ($obj->loaded())
			{
				return new Babble_API_Resource($this->remove_model_fields($obj->as_array()));
			}
			else
			{
				throw new API_Model_Exception('not found', '404-000');
			}
		}
		catch (API_Model_Exception $e)
		{
			// just rethrow API_Model_Exception's
			throw $e;
		}
		catch (ORM_Validation_Exception $e)
		{
			throw new API_Model_Exception($e->getMessage(), '400-100');
		}
		catch (Database_Exception $e)
		{
			$message = $e->getMessage();
			if (preg_match("/Duplicate entry '(.*)' for key.*/", $message, $match))
			{
				// TODO add substitution ability for api config values
				throw new API_Model_Exception($message, '400-000');
			}
			throw new API_Model_Exception($message, '500-100');
		}
		catch (Exception $e)
		{
			// for some reason, kohana decided to throw a general exception if you set a non-existent field.
			// wish they threw validation exception but whatever.
			$message = $e->getMessage();
			if (preg_match("/The (.*) property does not exist in the (.*) class/", $message, $match))
			{
				// TODO add substitution ability for api config values
				throw new API_Model_Exception($message, '400-101');
			}
			throw new API_Model_Exception($message, '500-200');
		}
	}

	/**
	 * @see parent::edit();
	 */
	public function edit($object_id, Babble_API_Resource $resource)
	{
		// catch all exceptions
		try
		{
			$obj = ORM::factory($this->model, $object_id);
			if ($obj->loaded())
			{
				foreach ($resource->get_data() AS $k => $v)
				{
					$obj->{$k} = $v;
				}

				if ($obj->save())
				{
					return 'Modified '.$obj->id;
				}
				else
				{
					throw new API_Model_Exception('failed to save', '500-201');
				}
			}
			else
			{
				throw new API_Model_Exception('not found', '404-000');
			}
		}
		catch (API_Model_Exception $e)
		{
			// just rethrow API_Model_Exception's
			throw $e;
		}
		catch (ORM_Validation_Exception $e)
		{
			throw new API_Model_Exception($e->getMessage(), '400-100');
		}
		catch (Database_Exception $e)
		{
			$message = $e->getMessage();
			if (preg_match("/Duplicate entry '(.*)' for key.*/", $message, $match))
			{
				// TODO add substitution ability for api config values
				throw new API_Model_Exception($message, '400-000');
			}
			throw new API_Model_Exception($message, '500-100');
		}
		catch (Exception $e)
		{
			// for some reason, kohana decided to throw a general exception if you set a non-existent field.
			// wish they threw validation exception but whatever.
			$message = $e->getMessage();
			if (preg_match("/The (.*) property does not exist in the (.*) class/", $message, $match))
			{
				// TODO add substitution ability for api config values
				throw new API_Model_Exception($message, '400-101');
			}
			throw new API_Model_Exception($message, '500-200');
		}
	}

	/**
	 * @see parent::add();
	 */
	public function add(Babble_API_Resource $resource)
	{
		// catch all exceptions
		try
		{
			$obj = ORM::factory($this->model);
			foreach ($resource->get_data() as $k => $v)
			{
				$obj->{$k} = $v;
			}
			if ($obj->save())
			{
				return 'Created '.$obj->id;
			}
			else
			{
				throw new API_Model_Exception('failed to save', '500-201');
			}
		}
		catch (API_Model_Exception $e)
		{
			// just rethrow API_Model_Exception's
			throw $e;
		}
		catch (ORM_Validation_Exception $e)
		{
			throw new API_Model_Exception($e->getMessage(), '400-100');
		}
		catch (Database_Exception $e)
		{
			$message = $e->getMessage();
			if (preg_match("/Duplicate entry '(.*)' for key.*/", $message, $match))
			{
				// TODO add substitution ability for api config values
				throw new API_Model_Exception($message, '400-000');
			}
			throw new API_Model_Exception($message, '500-100');
		}
		catch (Exception $e)
		{
			// for some reason, kohana decided to throw a general exception if you set a non-existent field.
			// wish they threw validation exception but whatever.
			$message = $e->getMessage();
			if (preg_match("/The (.*) property does not exist in the (.*) class/", $message, $match))
			{
				// TODO add substitution ability for api config values
				throw new API_Model_Exception($message, '400-101');
			}
			throw new API_Model_Exception($message, '500-200');
		}
	}

	/**
	 * @see parent::delete();
	 */
	public function delete($object_id)
	{
		// catch all exceptions
		try
		{
			$obj = ORM::factory($this->model, $object_id);
			if ($obj->loaded())
			{
				if ($obj->delete())
				{
					return 'Deleted '.$object_id;
				}
			}
			else
			{
				throw new API_Model_Exception('not found', '404-000');
			}
		}
		catch (ORM_Validation_Exception $e)
		{
			throw new API_Model_Exception($e->getMessage(), '400-100');
		}
		catch (Database_Exception $e)
		{
			$message = $e->getMessage();
			if (preg_match("/Duplicate entry '(.*)' for key.*/", $message, $match))
			{
				// TODO add substitution ability for api config values
				throw new API_Model_Exception($message, '400-000');
			}
			throw new API_Model_Exception($message, '500-100');
		}
		catch (Exception $e)
		{
			// for some reason, kohana decided to throw a general exception if you set a non-existent field.
			// wish they threw validation exception but whatever.
			$message = $e->getMessage();
			if (preg_match("/The (.*) property does not exist in the (.*) class/", $message, $match))
			{
				// TODO add substitution ability for api config values
				throw new API_Model_Exception($message, '400-101');
			}
			throw new API_Model_Exception($message, '500-200');
		}
	}
}
