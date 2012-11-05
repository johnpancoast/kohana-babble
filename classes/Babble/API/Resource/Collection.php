<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Collection of API resources
 */
class Babble_API_Resource_Collection extends ArrayObject {
	/**
	 * get collection as arrays
	 * @access public
	 * @return array
	 */
	public function as_array()
	{
		$arr = array();
		foreach ($this AS $rel => $rsc)
		{
			if ($rsc instanceof Babble_API_Resource_Collection)
			{
				foreach ($rsc as $r)
				{
					$arr[$rel][] = $r->as_array();
				}
			}
			else
			{
				$arr[$rel] = $rsc->as_array();
			}
		}
		return $arr;
	}

	/**
	 * add a key/val pair. overrides parent to ensure value is resource instance
	 * @access public
	 * @param mixed $index The array key
	 * @param mixed $newval The value
	 * @see http://www.php.net/manual/en/arrayobject.offsetset.php
	 */
	public function offsetSet($index, $newval)
	{
		if ( ! ($newval instanceof Babble_API_Resource) && ! ($newval instanceof Babble_API_Resource_Collection))
		{
			throw new API_Resource_Exception('newval must be instance of Babble_API_Resource or Babble_API_Resource_Collection');
		}
		return parent::offsetSet($index, $newval);
	}

	/**
	 * add a value to array. overrides parent to ensure value is resource instance
	 * @access public
	 * @param Babble_API_Resource An instance of a babble resource
	 * @see http://www.php.net/manual/en/arrayobject.append.php
	 */
	public function append($value)
	{
		if ( ! ($value instanceof Babble_API_Resource) && ! ($value instanceof Babble_API_Resource_Collection))
		{
			throw new API_Resource_Exception('value must be instance of Babble_API_Resource or Babble_API_Resource_Collection');
		}
		return parent::append($value);
	}
}
