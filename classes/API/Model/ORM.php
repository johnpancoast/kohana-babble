<?php defined('SYSPATH') or die('No direct script access.');

/**
 * kohana orm specific api/model interaction class
 * @TODO !!!!!!! A LOT OF FIXING AND SECURITY HARDENING !!
 */
class API_Model_ORM extends API_Model {
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
			$obj = ORM::factory($this->model, $request->request_id);
			if ($obj->loaded())
			{
				$response->set_response('1', $obj->as_array());
			}
			else
			{
				$response->set_response('0');
			}
		}
		catch (ORM_Validation_Exception $e)
		{
			throw new API_Response_Exception($e->getMessage(), '-8001');
		}
		catch (Database_Exception $e)
		{
			$message = $e->getMessage();
			if (preg_match("/Duplicate entry '(.*)' for key.*/", $message, $match))
			{
				// TODO add substitution ability for api config values
				throw new API_Response_Exception($message, '-7001');
			}
			throw new API_Response_Exception($message, '-7000');
		}
		catch (Exception $e)
		{
			// for some reason, kohana decided to throw a general exception if you set a non-existent field.
			// wish they threw validation exception but whatever.
			$message = $e->getMessage();
			if (preg_match("/The (.*) property does not exist in the (.*) class/", $message, $match))
			{
				// TODO add substitution ability for api config values
				throw new API_Response_Exception($message, '-8002');
			}
			throw new API_Response_Exception($message, '-8000');
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
			$obj = ORM::factory($this->model, $request->request_id);
			if ($obj->loaded())
			{
				foreach ($request->request_post as $k => $v)
				{
					$obj->{$k} = $v;
				}
				if ($obj->save())
				{
					$response->set_response('1', '1');
				}
				else
				{
					throw new API_Response_Exception('failed to save', '-8003');
				}
			}
			else
			{
				$response->set_response('0');
			}
		}
		catch (ORM_Validation_Exception $e)
		{
			throw new API_Response_Exception($e->getMessage(), '-8001');
		}
		catch (Database_Exception $e)
		{
			$message = $e->getMessage();
			if (preg_match("/Duplicate entry '(.*)' for key.*/", $message, $match))
			{
				// TODO add substitution ability for api config values
				throw new API_Response_Exception($message, '-7001');
			}
			throw new API_Response_Exception($message, '-7000');
		}
		catch (Exception $e)
		{
			// for some reason, kohana decided to throw a general exception if you set a non-existent field.
			// wish they threw validation exception but whatever.
			$message = $e->getMessage();
			if (preg_match("/The (.*) property does not exist in the (.*) class/", $message, $match))
			{
				// TODO add substitution ability for api config values
				throw new API_Response_Exception($message, '-8002');
			}
			throw new API_Response_Exception($message, '-8000');
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
			foreach ($request->request_post as $k => $v)
			{
				$obj->{$k} = $v;
			}
			if ($obj->save())
			{
				$response->set_response('1', $obj->id);
			}
			else
			{
				throw new API_Response_Exception('failed to save', '-8003');
			}
		}
		catch (ORM_Validation_Exception $e)
		{
			throw new API_Response_Exception($e->getMessage(), '-8001');
		}
		catch (Database_Exception $e)
		{
			$message = $e->getMessage();
			if (preg_match("/Duplicate entry '(.*)' for key.*/", $message, $match))
			{
				// TODO add substitution ability for api config values
				throw new API_Response_Exception($message, '-7001');
			}
			throw new API_Response_Exception($message, '-7000');
		}
		catch (Exception $e)
		{
			// for some reason, kohana decided to throw a general exception if you set a non-existent field.
			// wish they threw validation exception but whatever.
			$message = $e->getMessage();
			if (preg_match("/The (.*) property does not exist in the (.*) class/", $message, $match))
			{
				// TODO add substitution ability for api config values
				throw new API_Response_Exception($message, '-8002');
			}
			throw new API_Response_Exception($message, '-8000');
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
			$obj = ORM::factory($this->model, $request->request_id);
			if ($obj->loaded())
			{
				$obj->delete();
				$response->set_response('1', '1');
			}
			else
			{
				$response->set_response('0');
			}
		}
		catch (ORM_Validation_Exception $e)
		{
			throw new API_Response_Exception($e->getMessage(), '-8001');
		}
		catch (Database_Exception $e)
		{
			$message = $e->getMessage();
			if (preg_match("/Duplicate entry '(.*)' for key.*/", $message, $match))
			{
				// TODO add substitution ability for api config values
				throw new API_Response_Exception($message, '-7001');
			}
			throw new API_Response_Exception($message, '-7000');
		}
		catch (Exception $e)
		{
			// for some reason, kohana decided to throw a general exception if you set a non-existent field.
			// wish they threw validation exception but whatever.
			$message = $e->getMessage();
			if (preg_match("/The (.*) property does not exist in the (.*) class/", $message, $match))
			{
				// TODO add substitution ability for api config values
				throw new API_Response_Exception($message, '-8002');
			}
			throw new API_Response_Exception($message, '-8000');
		}
	}
}
