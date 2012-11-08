<?php defined('SYSPATH') or die('No direct script access.');

/**
 * API Resource. Borrows heavily from the ideas laid forth in Hypertext Application Language.
 * @link http://stateless.co/hal_specification.html
 */
class Babble_API_Resource {
	/**
	 * @var array The resource data
	 * @access private
	 */
	private $data = array();

	/**
	 * @var Babble_API_Resource_Link_Collection A set of resource links.
	 * @access private
	 * @link http://en.wikipedia.org/wiki/HATEOAS
	 */
	private $links = NULL;

	/**
	 * @var Babble_API_Resource_Collection A set of embedded resources.
	 * @access private
	 */
	private $embedded_resources = NULL;

	/**
	 * constructor
	 * @access public
	 * @param array $data The resource data.
	 * @param Babble_API_Resource_Link_Collection A set of links
	 * @param Babble_API_Resource_Collection A set of embedded resources
	 * @param bool $create_self_link Whether or not we create a link to the URI of the current request.
	 */
	public function __construct(array $data = array(), Babble_API_Resource_Link_Collection $links = NULL, Babble_API_Resource_Collection $embedded_resources = NULL, $create_self_link = TRUE)
	{
		$this->data = $data;
		$this->links = $links ? $links : new API_Resource_Link_Collection;
		$this->embedded_resources = $embedded_resources ? $embedded_resources : new API_Resource_Collection;

		if ($create_self_link)
		{
			$this->add_link_array('_self', '/'.API_Request::factory()->kohana_request()->uri());
		}
	}

	/**
	 * get object as an array.
	 * @access public
	 * @return array
	 */
	public function as_array()
	{
		$ret = $this->get_data();

		$links = $this->links->as_array();
		$embedded = $this->embedded_resources->as_array();
		if ( ! empty($links))
		{
			$ret['_links'] = $links;
		}
		if ( ! empty($embedded))
		{
			$ret['_embedded'] = $embedded;
		}

		return $ret;
	}

	/**
	 * set resource data
	 * @access public
	 * @param array $data Resource data
	 */
	public function set_data(array $data = array())
	{
		$this->data = $data;
	}

	/**
	 * get resource data
	 * @access public
	 * @return array
	 */
	public function get_data()
	{
		return $this->data;
	}

	/**
	 * add a link
	 * @access public
	 * @param string $rel The link relation
	 * @param Babble_API_Resource_Link $link A link object
	 */
	public function add_link($rel, Babble_API_Resource_Link $link)
	{
		// determine value. it's either the passed resource or a resource collection.
		$exists = $this->links->offsetExists($rel);
		if ($exists)
		{
			$cur = $this->links->offsetGet($rel);
			if ($cur instanceof Babble_API_Resource_Link)
			{
				$val = new API_Resource_Link_Collection;
				$val->append($cur);
			}
			else
			{
				$val = $cur;
			}

			$val->append($link);
		}
		else
		{
			$val = $link;
		}
		$this->links->offsetSet($rel, $val);
	}

	/**
	 * add link via passed params
	 * @link http://stateless.co/hal_specification.html
	 * @access public
	 * @param string $rel The link relation
	 * @param string $href The link location
	 * @param string $title Link title
	 * @param string $name Link name
	 * @param bool $templated Is the link templated
	 */
	public function add_link_array($rel, $href, $title = NULL, $name = NULL, $templated = NULL)
	{
		$link = new API_Resource_Link($rel, $href, $title, $name, $templated);
		$this->add_link($rel, $link);
	}

	/**
	 * get links
	 * @access public
	 * @return Babble_API_Resource_Link_Collection
	 */
	public function get_links()
	{
		return $this->links;
	}

	/**
	 * add embedded resource
	 * @link http://stateless.co/hal_specification.html
	 * @access public
	 * @param string $rel Link relation
	 * @param Babble_API_Resource The embedded resource
	 */
	public function add_embedded_resource($rel, Babble_API_Resource $resource)
	{
		// determine value. it's either the passed resource or a resource collection.
		$exists = $this->embedded_resources->offsetExists($rel);
		if ($exists)
		{
			$cur = $this->embedded_resources->offsetGet($rel);
			if ($cur instanceof Babble_API_Resource)
			{
				$val = new API_Resource_Collection;
				$val->append($cur);
			}
			else
			{
				$val = $cur;
			}

			$val->append($resource);
		}
		else
		{
			$val = $resource;
		}
		$this->embedded_resources->offsetSet($rel, $val);
	}

	/**
	 * get embedded resources
	 * @access public
	 * @return Babble_API_Resource_Collection
	 */
	public function get_embedded_resources()
	{
		return $this->embedded_resources;
	}
}
