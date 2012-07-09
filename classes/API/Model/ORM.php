<?php defined('SYSPATH') or die('No direct script access.');

/**
 * kohana orm specific api/model interaction class
 */
class API_Model_ORM extends API_Model {
	/**
	 * @see parent::get();
	 */
	public function get($id)
	{
		$obj = ORM::factory($this->model, $id);
		if ($obj->loaded())
		{
			return $obj->as_array();
		}
		return FALSE;
	}

	/**
	 * @see parent::edit();
	 */
	public function edit($id, array $params = array())
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
	public function add(array $params = array())
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
	public function delete($id)
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
