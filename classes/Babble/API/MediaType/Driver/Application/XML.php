<?php defined('SYSPATH') or die('No direct script access.');

/**
 * media type - application/xml
 * @see http://www.php.net/manual/en/book.dom.php
 */
class Babble_API_MediaType_Driver_Application_XML extends API_MediaType {
	/**
	 * @see API_MediaType::media_type
	 */
	protected $media_type = 'application/xml';

	/**
	 * @var bool Do we format xml output
	 */
	private $format_output = TRUE;

	/**
	 * recursive method to create & get a DOMElement from a resource object (and its embedded objects).
	 * @access private
	 * @param DOMDocument $dom An instance of the dom document we're creating.
	 * @param Babble_API_Resource $resource A babble resource.
	 * @param string $embedded_resource_rel Embedded resource's link relation.
	 * @return DOMElement
	 */
	private function get_resource_element(DOMDocument $dom, Babble_API_Resource $resource, $embedded_resource_rel = NULL)
	{
		// resource data
		$data = $resource->get_data();

		// hateoas links
		$links = $resource->get_links();
		$self = $links->offsetExists('_self') ? $links->offsetGet('_self')->as_array() : array('href' => NULL);

		// embedded resources
		$rscs = $resource->get_embedded_resources();

		// main resource element
		$el = $dom->createElement('resource');
		if ( ! empty($self['href']))
		{
			$el->setAttribute('href', $self['href']);
		}
		if ($embedded_resource_rel)
		{
			$el->setAttribute('rel', $embedded_resource_rel);
		}

		// add links
		$link_keys = array('href', 'templated');
		foreach ($links AS $rel => $link)
		{
			// skip self links since they are written in the resource element itself.
			if ($rel == '_self')
			{
				continue;
			}

			$link = $link->as_array();
			$ellink = $el->appendChild($dom->createElement('link'));
			$ellink->setAttribute('rel', $rel);
			foreach ($link_keys AS $k)
			{
				if (isset($link[$k]))
				{
					$ellink->setAttribute($k, $link[$k]);
				}
			}
		}

		// add data
		foreach ($data AS $k => $v)
		{
			$el->appendChild($dom->createElement($k, $v));
		}

		// recursively add embedded resources
		foreach ($rscs AS $rel => $rsc)
		{
			$el->appendChild($this->get_resource_element($dom, $rsc, $rel));
		}

		return $el;
	}

	/**
	 * recursive method to create & get a resource object (and its embedded resources) from a DOMElement
	 * @param DOMElement $elrsc DOM element
	 * @return Babble_API_Resource
	 */
	private function get_element_resource(DOMElement $elrsc)
	{
		// resource object we're building.
		$rsc = new API_Resource;

		// make a new dom doc containing this element. we need a document for
		// searching since we're matching paths with xpath.
		// FIXME allow for charset to be accepted from header
		$dom = new DOMDocument('1.0', 'US-ASCII');
		$dom->appendChild($dom->importNode($elrsc, TRUE));
		$xpath = new DOMXPath($dom);

		// the href of a resource element is actually the _self link
		if ($elrsc->hasAttribute('href'))
		{
			$rsc->add_link_array('_self', $elrsc->getAttribute('href'));
		}

		// loop all parent elements and create resource object. a <resource> element
		// will be recursively created.
		foreach ($xpath->query('/resource/*') AS $el)
		{
			switch ($el->nodeName)
			{
				case 'link':
					$rel = $el->hasAttribute('rel') ? $el->getAttribute('rel') : 'undefined';
					$href = $el->hasAttribute('href') ? $el->getAttribute('href') : 'undefined';
					$title = $el->hasAttribute('title') ? $el->getAttribute('title') : NULL;
					$name = $el->hasAttribute('name') ? $el->getAttribute('name') : NULL;
					$templated = $el->hasAttribute('templated') ? $el->getAttribute('templated') : NULL;
					if ($rel != '_self')
					{
						$rsc->add_link_array($rel, $href, $title, $name, $templated);
					}
					break;
				case 'resource':
					$rel = $el->hasAttribute('rel') ? $el->getAttribute('rel') : 'undefined';
					$rsc->add_embedded_resource($rel, $this->get_element_resource($el));
					break;
				default:
					$data[$el->nodeName] = $el->nodeValue;
					break;
			}
		}

		$rsc->set_data($data);

		return $rsc;
	}

	/**
	 * @see parent::_get_encoded_resource()
	 */
	protected function _get_encoded_resource(Babble_API_Resource $resource)
	{
		// begin document
		// FIXME allow for charset to be accepted from header
		$dom = new DOMDocument('1.0', 'US-ASCII');
		$dom->formatOutput = $this->format_output;

		// append children (handle recursion for embedded resources)
		$dom->appendChild($this->get_resource_element($dom, $resource));

		return $dom->saveXml();
	}

	/**
	 * @see parent::_get_decoded_resource()
	 */
	protected function _get_decoded_resource($data = NULL)
	{
		// get the resource element passed
		// FIXME allow for charset to be accepted from header
		$dom = new DOMDocument('1.0', 'US-ASCII');
		$dom->formatOutput = $this->format_output;
		$dom->loadXml($data);

		// get matched resources
		$xpath = new DOMXPath($dom);
		$rscs = $xpath->query('/resource');
		if ($rscs->length != 1)
		{
			throw new API_MediaType_Exception_Encoding('too many resources passed, confused!', '400-200');
		}

		// we're just using the first (and only) of the loop
		foreach ($rscs AS $rsc)
		{
			return $this->get_element_resource($rsc);
		}
	}
}
