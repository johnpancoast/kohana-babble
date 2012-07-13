<?php defined('SYSPATH') or die('No direct script access.');

/**
 * kohana orm specific api/model interaction class
 */
class API_Model_ORM extends API_Model {
	/**
	 * @see parent::get();
	 */
	public function _get()
	{
		$request = API_Request::factory();
		$response = API_Response::factory();

		$obj = ORM::factory($this->model, $request->request_id);
		// catch all exceptions
		try
		{
			if ($obj->loaded())
			{
				$response->set_response('1', $obj->as_array());
			}
			else
			{
				$response->set_response('0');
			}
		}
		catch (Exception $e)
		{
			throw new API_Response_Exception('ORM exception', '-99997');
		}
	}

	/**
	 * @see parent::edit();
	 */
	public function _edit($id, array $params = array())
	{
		$obj = ORM::factory($this->model, $id);
		if ($obj->loaded())
		{
			foreach ($params as $k => $v)
			{
				$obj->{$k} = $v;
			}

			$obj->save();

			// TODO checks here

			return TRUE;
		}

		return FALSE;
	}

	/**
	 * @see parent::add();
	 */
	public function _add(array $params = array())
	{
		$obj = ORM::factory($this->model);
		foreach ($params as $k => $v)
		{
			$obj->{$k} = $v;
		}
		$obj->save();

		// TODO checks here

		return $obj->id;
	}

	/**
	 * @see parent::delete();
	 */
	public function _delete($id)
	{
		$obj = ORM::factory($this->model, $id);
		if ($obj->loaded())
		{
			$obj->delete();
			// TODO check
			return TRUE;
		}
		return FALSE;
	}
}
