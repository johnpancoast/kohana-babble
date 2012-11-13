# Resources
[Controller](requests#controllers) methods will generally set the response with an
[API_Resource] as seen in the [Getting Started](gettingstarted) section.
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

The [API_Resource] class also provides the ability to add embedded resources
and create links (adding support for [hypermedia as the engine of application
state](http://en.wikipedia.org/wiki/HATEOAS)). Babble adopted ideas found in
[Hypertext Application Language](http://stateless.co/hal_specification.html).

We'll continue using the "Hello, World" controller above.

## Embedded Resources
Embedded resources are additional resources you can send in the response. An
example use may be when you're responding with a user's "post" and you want to
also embed the post's comments. Embedded resources solve this.

To embed a resource, you create a new resource object and add it to the parent
resource using the
[add_embedded_resource()](http://pilot.xxx/guide-api/API_Resource#add_embedded_resource)
method. The first param is the "relation" and the final param is the embedded
resource. Here's an example.

~~~
<?php defined('SYSPATH') or die('No direct script access.');
 
class Controller_Public_API_Helloworld extends Controller_API {
    public function action_get()
    {   
        $rsc = new API_Resource(array(
            'greeting' => 'Hello',
            'planet' => 'World'
        ));

		// add embedded resource
		$embedded_rsc = new API_Resource(array(
			'internal' => 'foobar'
		));
		$rsc->add_embedded_resource('my_relation', $embedded_rsc);
 
        $this->api_response->set_response('200-000', $rsc);
    }   
}
~~~

A request sent to /api/helloworld would give responses like so.

_application/json_
~~~
{
    "greeting": "Hello",
    "planet": "World",
    "_embedded": {
        "my_relation": {
            "internal": "foobar"
        }
    }
}
~~~
_application/xml_
~~~
<?xml version="1.0" encoding="US-ASCII"?>
<resource>
  <greeting>Hello</greeting>
  <planet>World</planet>
  <resource rel="my_relation">
    <internal>foobar</internal>
  </resource>
</resource>
~~~

You can nest embedded resources as far as you want.

## Links
Links are an important part of a REST API since they allow for
[hypermedia as the engine of application state](http://en.wikipedia.org/wiki/HATEOAS).

You can add a link using the
[add_link()](http://pilot.xxx/guide-api/API_Resource#add_link) or
[add_link_array()](http://pilot.xxx/guide-api/API_Resource#add_link_array)
methods.

Here's an example controller and the request results.
~~~
<?php defined('SYSPATH') or die('No direct script access.');
 
class Controller_Public_API_Helloworld extends Controller_API {
    public function action_get()
    {   
        $rsc = new API_Resource(array(
            'greeting' => 'Hello',
            'planet' => 'World'
        ));

		// add link
		$rsc_link = new API_Resource_Link('http://my/uri', 'my_title', 'my_name', FALSE);
		$rsc->add_link('my_rel', $rsc_link);
		// ... this would do ths same ...
		//$rsc->add_link_array('my_rel', 'http://my/uri', 'my_title', 'my_name', FALSE);

		// add embedded resource
		$embedded_rsc = new API_Resource(array(
			'internal' => 'foobar'
		));
		$rsc->add_embedded_resource('my_relation', $embedded_rsc);
 
        $this->api_response->set_response('200-000', $rsc);
    }   
}
~~~
_application/json_
~~~
{
    "greeting": "Hello",
    "planet": "World",
    "_links": {
        "my_rel": {
            "href": "http://my/uri",
            "title": "my_title",
            "name": "my_name"
        }
    },
    "_embedded": {
        "my_relation": {
            "internal": "foobar"
        }
    }
}
~~~
_application/xml_
~~~
<?xml version="1.0" encoding="US-ASCII"?>
<resource>
  <link rel="my_rel" href="http://my/uri" title="my_title" name="my_name"/>
  <greeting>Hello</greeting>
  <planet>World</planet>
  <resource rel="my_relation">
    <internal>foobar</internal>
  </resource>
</resource>
~~~

### Self links
One important idea is that resources should link to themselves. We call this a
"self link" or "_self". A self link is simply a link that is set with a relation
of "_self". You can also use the
[add_self_link()](http://pilot.xxx/guide-api/API_Resource#add_self_link) method
which will assign the URI of the request as _self.

Here's an example controller and the request results.
~~~
<?php defined('SYSPATH') or die('No direct script access.');
 
class Controller_Public_API_Helloworld extends Controller_API {
    public function action_get()
    {   
        $rsc = new API_Resource(array(
            'greeting' => 'Hello',
            'planet' => 'World'
        ));

		// add self link
		// assuming we're accessing this controller via /api/helloworld then
		// that should be the value assigned here.
		$rsc->add_self_link();
		// ... this would do the same ...
		//$this->add_link_array('_self', '/api/helloworld');

		// add link
		$rsc_link = new API_Resource_Link('http://my/uri', 'my_title', 'my_name', FALSE);
		$rsc->add_link('my_rel', $rsc_link);
		// ... this would do ths same ...
		//$rsc->add_link_array('my_rel', 'http://my/uri', 'my_title', 'my_name', FALSE);

		// add embedded resource
		$embedded_rsc = new API_Resource(array(
			'internal' => 'foobar'
		));
		$rsc->add_embedded_resource('my_relation', $embedded_rsc);
 
        $this->api_response->set_response('200-000', $rsc);
    }   
}
~~~
_application/json_
~~~
{
    "greeting": "Hello",
    "planet": "World",
    "_links": {
        "_self": {
            "href": "/api/helloworld"
        },
        "my_rel": {
            "href": "http://my/uri",
            "title": "my_title",
            "name": "my_name"
        }
    },
    "_embedded": {
        "my_relation": {
            "internal": "foobar"
        }
    }
}
~~~
_application/xml_
~~~
<?xml version="1.0" encoding="US-ASCII"?>
<resource href="/api/helloworld">
  <link rel="my_rel" href="http://my/uri" title="my_title" name="my_name"/>
  <greeting>Hello</greeting>
  <planet>World</planet>
  <resource rel="my_relation">
    <internal>foobar</internal>
  </resource>
</resource>
~~~

Next Section: [Media Types](mediatypes)
