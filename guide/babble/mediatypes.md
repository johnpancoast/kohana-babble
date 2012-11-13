# Media Types
Babble supports content negotiation which means that you can have one document
(resource) be served in different [media
types](http://www.iana.org/assignments/media-types/index.html). In Babble a
media type is a media type class located in the `classes/API/MediaType/Driver/`
directory. The class' location directly corresponds to the media type. For
example, look at these media types and where these class files would live.

* `application/xml` - classes/API/MediaType/Driver/Application/XML.php
* `text/html` - classes/API/MediaType/Driver/Text/Html.php
* `application/vnd.appname-v3+json` - classes/API/MediaType/Driver/Application/Vnd/Appname/JSON.php

[!!]Note that the last media type is a vendor media type that is passing `version 3`.
The version is not included in the class name. This is because the version means
this class will live in a different location. It will live in a "version
module" i.e.,
`APPPATH/babble-versions/3/classes/API/MediaType/Driver/Application/Vnd/Appname/JSON.php`.
If we passed `v2` this would live in 
`APPPATH/babble-versions/2/classes/API/MediaType/Driver/Application/Vnd/Appname/JSON.php`.
This is discussed more in the [Versions](versions) section.

Here is an example `application/json` media type.
~~~
<?php defined('SYSPATH') or die('No direct script access.');

/**
 * media type - application/json
 */
class API_MediaType_Driver_Application_JSON extends API_MediaType {
	/**
	 * @see API_MediaType::media_type
	 */
	protected $media_type = 'application/json';

	/**
	 * @see parent::_get_encoded_resource()
	 */
	protected function _get_encoded_resource(Babble_API_Resource $resource)
	{
		// do something here to take a babble resource
		// and encode it in this media types "format".
		// we should return a string.
	}

	/**
	 * @see parent::_get_decoded_resource()
	 */
	protected function _get_decoded_resource($data = NULL)
	{
		// do something here to take a string and decode it.
		// we should return a babble resource.
	}
}
~~~

So a few notes...

* A media type class lives in `classes/API/MediaType/Driver/
* It must extend [API_MediaType].
* It must have a `media_type` property defining what it is.
* It must have at least 2 methods.
	* _get_encoded_resource() - Takes a babble resource, "encodes" it, and returns a string.
	* _get_decoded_resource() - Take a string, "decodes" it, and returns a babble resource created by the decoded string.

So you can make as many media types as you wish. Your application (or your
version module) can have custom types etc. You can also extend the existing
Babble media types and modify them how you wish

## Passing or Requesting a Certain Media Type
In Babble you can request a document of a certain media type by using the
[Accept](http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.1)
header. If Babble is unable to respond with any of the media types that were
passed in the Accept header then it will respond with a
[406 status code](http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.4.7). You
specify a document (i.e., POST or PUT) of a certain media type by using the
[Content-Type](http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.17)
header. If Babble cannot handle the media type it will respond with a [415
status code](http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.4.16).

Babble will set the `Content-Type` header of the media type it is responding
with. For example, if your `Accept` header asks for `application/json` and
Babble can (and does) respond with that media type, then the `Content-Type`
header of the response will have a value of `application/json`.

## Specifying Default Media Types
Babble allows you to match passed Accept of Content-Type headers and use a media
type class that you decide. These are defined using the `default_media_types`
config value. The key is the media type to match and the value is the media type
to respond with. By default, Babble sets `'*/*' => 'application/json'` meaning
that if an Accept header has `*/*` and no other media types of the Accept header
were matched, then application/json will be served.

[!!]Note that the "version" portion passed in a media type is ignored when matching
types in this config value.

Next Section: [Versions](versions)
