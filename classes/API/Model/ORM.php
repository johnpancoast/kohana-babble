<?php defined('SYSPATH') or die('No direct script access.');

/**
 * kohana orm specific api/model interaction class
 * @TODO !!!!!!! A LOT OF FIXING AND SECURITY HARDENING !!
 */
class API_Model_ORM extends API_Model {
	/**
	 * @see parent::get_list();
	 * TODO - this method is incomplete. needs support for limit, sorting, and searching
	 */
	public function get_list()
	{
		// Be sure to only profile if it's enabled
		/*
		if (Kohana::$profiling === TRUE)
		{
			// Start a new benchmark
			$benchmark = Profiler::start('Your Category', __FUNCTION__);
		}
		*/

		$request = API_Request::factory();
		$response = API_Response::factory();
		$passed_limit = Request::current()->query('limit');
		$passed_page = Request::current()->query('page');
		$limit = $passed_limit ? $passed_limit : 100;
		$offset = $passed_page ? $passed_page * $limit : 0;

		// catch all exceptions
		try
		{
			$objs = ORM::factory($this->model)
				->limit($limit)
				->offset($offset);
			$result = $objs->find_all();
			$resp = array();
			foreach ($result as $row)
			{
				$resp[] = $row->as_array();
			}
			$response->set_response('200-000', $resp);
		}
		catch (ORM_Validation_Exception $e)
		{
			throw new API_Response_Exception($e->getMessage(), '400-100');
		}
		catch (Database_Exception $e)
		{
			$message = $e->getMessage();
			throw new API_Response_Exception($message, '500-100');
		}
		catch (Exception $e)
		{
			// for some reason, kohana decided to throw a general exception if you set a non-existent field.
			// wish they threw validation exception but whatever.
			$message = $e->getMessage();
			throw new API_Response_Exception($message, '500-200');
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
	public function get()
	{
		$request = API_Request::factory();
		$response = API_Response::factory();

		// catch all exceptions
		try
		{
			$obj = ORM::factory($this->model, $request->request_resource_id);
			if ($obj->loaded())
			{
				$response->set_response('200-000', $obj->as_array());
			}
			else
			{
				$response->set_response('404-000');
			}
		}
		catch (ORM_Validation_Exception $e)
		{
			throw new API_Response_Exception($e->getMessage(), '400-100');
		}
		catch (Database_Exception $e)
		{
			$message = $e->getMessage();
			if (preg_match("/Duplicate entry '(.*)' for key.*/", $message, $match))
			{
				// TODO add substitution ability for api config values
				throw new API_Response_Exception($message, '400-000');
			}
			throw new API_Response_Exception($message, '500-100');
		}
		catch (Exception $e)
		{
			// for some reason, kohana decided to throw a general exception if you set a non-existent field.
			// wish they threw validation exception but whatever.
			$message = $e->getMessage();
			if (preg_match("/The (.*) property does not exist in the (.*) class/", $message, $match))
			{
				// TODO add substitution ability for api config values
				throw new API_Response_Exception($message, '400-101');
			}
			throw new API_Response_Exception($message, '500-200');
		}
	}

	/**
	 * @see parent::edit();
	 */
	public function edit()
	{
		$request = API_Request::factory();
		$response = API_Response::factory();

		// catch all exceptions
		try
		{
			$obj = ORM::factory($this->model, $request->request_resource_id);
			if ($obj->loaded())
			{
				foreach ($request->request_resource_data as $k => $v)
				{
					$obj->{$k} = $v;
				}
				if ($obj->save())
				{
					$response->set_response('200-000');
				}
				else
				{
					throw new API_Response_Exception('failed to save', '500-201');
				}
			}
			else
			{
				$response->set_response('0');
			}
		}
		catch (ORM_Validation_Exception $e)
		{
			throw new API_Response_Exception($e->getMessage(), '400-100');
		}
		catch (Database_Exception $e)
		{
			$message = $e->getMessage();
			if (preg_match("/Duplicate entry '(.*)' for key.*/", $message, $match))
			{
				// TODO add substitution ability for api config values
				throw new API_Response_Exception($message, '400-000');
			}
			throw new API_Response_Exception($message, '500-100');
		}
		catch (Exception $e)
		{
			// for some reason, kohana decided to throw a general exception if you set a non-existent field.
			// wish they threw validation exception but whatever.
			$message = $e->getMessage();
			if (preg_match("/The (.*) property does not exist in the (.*) class/", $message, $match))
			{
				// TODO add substitution ability for api config values
				throw new API_Response_Exception($message, '400-101');
			}
			throw new API_Response_Exception($message, '500-200');
		}
	}

	/**
	 * @see parent::add();
	 */
	public function add()
	{
		$request = API_Request::factory();
		$response = API_Response::factory();

		// catch all exceptions
		try
		{
			$obj = ORM::factory($this->model);
			foreach ($request->request_resource_data as $k => $v)
			{
				$obj->{$k} = $v;
			}
			if ($obj->save())
			{
				$response->set_response('200-000', $obj->id);
			}
			else
			{
				throw new API_Response_Exception('failed to save', '500-201');
			}
		}
		catch (ORM_Validation_Exception $e)
		{
			throw new API_Response_Exception($e->getMessage(), '400-100');
		}
		catch (Database_Exception $e)
		{
			$message = $e->getMessage();
			if (preg_match("/Duplicate entry '(.*)' for key.*/", $message, $match))
			{
				// TODO add substitution ability for api config values
				throw new API_Response_Exception($message, '400-000');
			}
			throw new API_Response_Exception($message, '500-100');
		}
		catch (Exception $e)
		{
			// for some reason, kohana decided to throw a general exception if you set a non-existent field.
			// wish they threw validation exception but whatever.
			$message = $e->getMessage();
			if (preg_match("/The (.*) property does not exist in the (.*) class/", $message, $match))
			{
				// TODO add substitution ability for api config values
				throw new API_Response_Exception($message, '400-101');
			}
			throw new API_Response_Exception($message, '500-200');
		}
	}

	/**
	 * @see parent::delete();
	 */
	public function delete()
	{
		$request = API_Request::factory();
		$response = API_Response::factory();

		// catch all exceptions
		try
		{
			$obj = ORM::factory($this->model, $request->request_resource_id);
			if ($obj->loaded())
			{
				$obj->delete();
				$response->set_response('200-000');
			}
			else
			{
				$response->set_response('0');
			}
		}
		catch (ORM_Validation_Exception $e)
		{
			throw new API_Response_Exception($e->getMessage(), '400-100');
		}
		catch (Database_Exception $e)
		{
			$message = $e->getMessage();
			if (preg_match("/Duplicate entry '(.*)' for key.*/", $message, $match))
			{
				// TODO add substitution ability for api config values
				throw new API_Response_Exception($message, '400-000');
			}
			throw new API_Response_Exception($message, '500-100');
		}
		catch (Exception $e)
		{
			// for some reason, kohana decided to throw a general exception if you set a non-existent field.
			// wish they threw validation exception but whatever.
			$message = $e->getMessage();
			if (preg_match("/The (.*) property does not exist in the (.*) class/", $message, $match))
			{
				// TODO add substitution ability for api config values
				throw new API_Response_Exception($message, '400-101');
			}
			throw new API_Response_Exception($message, '500-200');
		}
	}
}
