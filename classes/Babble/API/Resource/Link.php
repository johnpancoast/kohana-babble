<?php defined('SYSPATH') or die('No direct script access.');

/**
 * API HATEOAS Link
 */
class Babble_API_Resource_Link {
	private $link = NULL;
	private $rel = NULL;
	private $title = NULL;
	public function __construct($link, $rel = NULL, $title = NULL)
	{
		$this->link = $link;
		$this->rel = $rel;
		$this->title = $title;
	}
}
