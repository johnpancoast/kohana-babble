<?php defined('SYSPATH') or die('No direct script access.');

/**
 * API Resource
 */
class Babble_API_Resource {
	private $data = array();
	private $links = NULL;
	private $embedded_resources = NULL;

	public function __construct()
	{
		$this->links = new Babble_API_Resource_Link_Collection;
		$this->embedded_resources = new Babble_API_Resource_Collection;
	}

	public function set_data(array $data = NULL)
	{
		$this->data = $data;
	}

	public function add_link(Babble_API_Resource_Link $link)
	{
		$this->links->append($link);
	}

	public function add_link_array($link, $rel = NULL, $title = NULL)
	{
		$link = new Babble_API_Resource_Link($link, $rel, $title);
		$this->add_link($link);
	}

	public function get_links()
	{
		return $this->links;
	}

	public function add_embedded_resource(Babble_API_Resource $resource)
	{
		$this->embedded_resources->append($resource);
	}

	public function get_embedded_resources()
	{
		return $this->embedded_resources;
	}
}
