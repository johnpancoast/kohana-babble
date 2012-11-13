<?php defined('SYSPATH') or die('No direct script access.');

/**
 * API HATEOAS Link
 * @link http://en.wikipedia.org/wiki/HATEOAS
 */
class Babble_API_Resource_Link {
	/**
	 * @var string $href Link href
	 * @access private
	 */
	private $href = NULL;

	/**
	 * @var string $title Link title
	 * @access private
	 */
	private $title = NULL;

	/**
	 * @var string $name Link name
	 * @access private
	 */
	private $name = NULL;

	/**
	 * @var string $templated Link templated
	 * @access private
	 */
	private $templated = FALSE;

	/**
	 * constructor
	 * @param string $href The link location
	 * @param string $title Link title
	 * @param string $name Link name
	 * @param bool $templated Is the link templated
	 */
	public function __construct($href, $title = NULL, $name = NULL, $templated = FALSE)
	{
		$this->set_href($href);
		$this->set_title($title);
		$this->set_name($name);
		$this->set_templated($templated);
	}

	/**
	 * get link as array
	 * @return array
	 */
	public function as_array()
	{
		$href = $this->get_href();
		$title = $this->get_title();
		$name = $this->get_name();
		$templated = $this->get_templated();

		$arr = array('href' => $href);
		if ($title)
		{
			$arr['title'] = $title;
		}
		if ($name)
		{
			$arr['name'] = $name;
		}
		if ($templated)
		{
			$arr['templated'] = $templated;
		}
		return $arr;
	}

	/**
	 * set link href
	 * @access public
	 */
	public function set_href($href)
	{
		$this->href = $href;
	}

	/**
	 * set link title
	 * @access public
	 */
	public function set_title($title)
	{
		$this->title = $title;
	}

	/**
	 * set link name
	 * @access public
	 */
	public function set_name($name)
	{
		$this->name = $name;
	}

	/**
	 * set link templated
	 * @access public
	 */
	public function set_templated($templated)
	{
		$this->templated = (bool)$templated;
	}

	/**
	 * get link href
	 * @access public
	 * @return string
	 */
	public function get_href()
	{
		return $this->href;
	}

	/**
	 * get link title
	 * @access public
	 * @return string
	 */
	public function get_title()
	{
		return $this->title;
	}

	/**
	 * get link name
	 * @access public
	 * @return string
	 */
	public function get_name()
	{
		return $this->name;
	}

	/**
	 * get link templated
	 * @access public
	 * @return bool
	 */
	public function get_templated()
	{
		return $this->templated;
	}
}
