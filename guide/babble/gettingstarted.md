# Getting Started

This section should provide enough details to get you a bit familiarized with
Babble and how it works.

## Using a REST Client

Since we'll be making API requests to a REST server that you'll be creating,
you'll likely want an easy to use REST client. If you don't already have a
client that you use, I'd recommend the
[Restify](https://github.com/morgan/kohana-restify) Kohana module. Here's an
[example of how it looks](http://restify.io/). I've used this module a lot while
developing Babble since it allows me to quickly change URIs, headers, request
methods and the request body.

## Configuration

1. Copy the babble config to your APPPATH/config/ directory. E.g., on a nix
machine `cp config/babble.php APPPATH/config/`.
1. Make version 1 (or 1.0 or 1.0.0 etc) of your API. This section is just to get
you started.  See the [Versions](versions) section for more details. A version
is nothing more than a Kohana module. It's suggested to create a directory in
your application that will hold all of your API versions.
	- There is a directory to get you started located at misc/babble-versions.  Copy
	this directory to your APPPATH. E.g. on a *nix machine `cp -R
	misc/babble-versions APPPATH/`.
	- Inside APPPATH/babble-versions/ you'll notice there is a directory called '1'.
	This is version 1 of your API. You can of course rename this to whatever you'd
	like (e.g., 1.0 etc).
	- Add this version to your config. Open APPPATH/config/babble.php and set the
	`versions` and `current_version` config values.
	~~~
	// ... other code here

	/**
	 * [versions]
	 *
	 * All API versions. The keyed version string should _only_ contain numbers and .'s.
	 */
	'versions' => array(
		'1' => APPPATH.'babble-versions/1',
	),

	/**
	 * [current_version]
	 *
	 * the current version of the API. When clients do not pass a version,
	 * this is the version used where applicable..
	 */
	'current_version' => '1',

	// ... other code here
	~~~

	- How this version gets loaded is discussed more in the [Versions](versions)
	section. For now, just know that this version will be loaded until you change
	the `current_version` setting or pass a different version in the Accept or
	Content-Type headers.
	- Controllers for version 1 of your API should live in
	  APPPATH/babble-versions/1/classes/Controller/Public/API/

[!!]Since API versions are just loaded as Kohana modules you could,
theoretically, ignore versioning altogether and place your application's API
related code in your APPPATH, however, this is discouraged unless you are
overriding general Babble classes.

## Hello, World
Now that you have version 1 configured, let's create a Hello, World.

1. The first thing you'll need to do is disable the authentication that normally
occurs. You can see the [Authentication](authentication) section for more details. For now,
you can set the `authentication` value to FALSE in your babble config (which
should now be located at APPPATH/config/babble.php).
1. Place the following code in a file at
`APPPATH/babble-versions/1/classes/Controller/Public/API/Helloworld.php`.
	~~~
	<?php defined('SYSPATH') or die('No direct script access.');

	class Controller_Public_API_Helloworld extends Controller_API {
		public function action_get()
		{   
			$rsc = new API_Resource(array(
				'greeting' => 'Hello',
				'planet' => 'World'
			));

			$this->api_response->set_response('200-000', $rsc);
		}   
	}
	~~~
	A few notes about the controller...
	- It lives in classes/Controller/Public/API/. That's where all Babble API controllers will live.
	- It extends [Controller_API]. All controller classes must extend either
	  [Controller_API] or [Controller_API_Model] (discussed later).
	- It has a method called action_get(). All [request
	  methods](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html) will be
	  routed to a respective controller method i.e., The POST request method is
	  routed to action_post().
	- It creates a new [API_Resource] and assigns some data.
	- Using $this->api_response, it sets a response by passing a babble [message
	  code](messagecodes) and an [API_Resource] object. You can also pass a string message
	  instead of the resource object and a resource object will be created for
	  you.

	[!!]All controller classes that extend [Controller_API] or [Controller_API_Model]
	will have `$this->api_request` and `$this->api_response` available. These are
	[API_Request] and [API_Response] respectively.

1. URIs beginning with `/api` will route to api controllers in
`classes/Controller/Public/API/`. So for example, a request to `/api/helloworld`
would be routed to the controller we just created. Let's make a request to
/api/helloworld similar to the following.
	~~~
	GET api/helloworld HTTP/1.1
	Host: http://example.com
	Referer: http://example.com
	Accept: application/json
	Content-Length: 0
	~~~
You should see a response like this.
	~~~
	Content-Type: application/json

	{"greeting":"Hello","planet":"World"}
	~~~
Note the `Accept` header in the request is requesting an entity in
`application/json` so that's what we receive if a media class exists to handle
the response (discussed later). Now let's change the Accept header to
`application/xml` and make the same request.
	~~~
	GET api/helloworld HTTP/1.1
	Host: http://example.com
	Referer: http://example.com
	Accept: application/xml
	Content-Length: 0
	~~~
You should see a response like this.
	~~~
	Content-Type: application/xml

	...<?xml declaration here (removed due to breaking userguide)...
	<resource>
		<greeting>Hello</greeting>
		<planet>World</planet>
	</resource>
	~~~

This is a simple example of how easy it is to create a resource and respond with
different content via content negotiation.

Next Section: [Authentication](authentication)
