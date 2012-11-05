<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Collection of API HATEOAS links
 * @link http://en.wikipedia.org/wiki/HATEOAS
 */
class Babble_API_Resource_Link_Collection extends ArrayObject {
	/**
	 * get links as array
	 * @access public
	 * @return array
	 */
	public function as_array()
	{
		$arr = array();
		foreach ($this AS $rel => $link)
		{
			if ($link instanceof Babble_API_Resource_Link_Collection)
			{
				foreach ($link as $l)
				{
					$arr[$rel][] = $l->as_array();
				}
			}
			else
			{
				$arr[$rel] = $link->as_array();
			}
		}
		return $arr;
	}

	/**
	 * add a key/val pair. overrides parent to ensure value is link instance
	 * @access public
	 * @param mixed $index The array key
	 * @param mixed $newval The value
	 * @see http://www.php.net/manual/en/arrayobject.offsetset.php
	 */
	public function offsetSet($index, $newval)
	{
		if ( ! ($newval instanceof Babble_API_Resource_Link) && ! ($newval instanceof Babble_API_Resource_Link_Collection))
		{
			throw new Babble_API_Resource_Link_Exception('newval must be instance of Babble_API_Resource_Link or Babble_API_Resource_Link_Collection');
		}
		return parent::offsetSet($index, $newval);
	}

	/**
	 * add a value to array. overrides parent to ensure value is resource instance
	 * @access public
	 * @param Babble_API_Resource_Link An instance of a babble resource
	 * @see http://www.php.net/manual/en/arrayobject.append.php
	 */
	public function append($value)
	{
		if ( ! ($value instanceof Babble_API_Resource_Link) && ! ($value instanceof Babble_API_Resource_Link_Collection))
		{
			throw new Babble_API_Resource_Link_Exception('value must be instance of Babble_API_Resource_Link or Babble_API_Resource_Link_Collection');
		}
		return parent::append($value);
	}
}
