# Model CRUD
Babble has the ability to quickly add model CRUD functionality. This has been
abstracted so you can put whatever ORM / model layer you want behind it. It
currently ships with support for Kohana ORM. 

To add Kohana ORM CRUD functionality, you simply create a controller class that
extends Controller_API_Model and sets a $model propery to the model it works
with. Let's assume we have a model called 'user' and we want to expose CRUD
operations via an API. We can create a controller like this.
~~~
<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Public_API_User extends Controller_API_Model { 
	protected $model = 'user';

	/* this is optional and allows you to set the model fields
	   that the API will respond with.
	   i.e., this would only respond with username and email fields.
	protected $model_fields = array('username', 'email');
	*/
}
~~~

That's it! So a few things to note...

- You must extend `Controller_API_Model`.
- You set the model you're working with using the $model property. The
  controller class name does _not_ need to match the model name.
- You can add an optional propertly called `model_fields` which will limit the
  model fields that are sent in the response to the fields defined in the
  array.
- There's no need to add controller methods since they are defined in
  [Controller_API_Model]. You can, however, override them when needed.

[!!]There is a future plan to allow setting model fields aliases that are sent
in the response instead of the model fields themselves.

## Adding to Response
It is sometimes desirable to add to the response of a Model CRUD method. For
example, perhaps you want to add
[links](resources#links) or [embedded
resources](resources#embedded-resources) to the
resource that the model assigned to the response.

You can do so like this.
~~~
<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Public_API_User extends Controller_API_Model { 
	protected $model = 'user';

	// override the parent's get method.
	public function action_get()
	{
		// call the parent so the normal model CRUD handling occurs
		parent::action_get();

		// get the resource that the model set
		$rsc = $this->api_response->resource();

		// nake whatever modifications to the resource...
		// Described in the Resources section.
		//
		// add a link
		// $rsc->add_link_array(... etc);
		//
		// add embedded resource
		// $child_rsc = new API_Resource(array('foo' => 'bar'));
		// $rsc->add_embedded_resource('rel', $child_rsc);

		// reassign this modified resource to the response object.
		// note that we do not use the set_response() method but use
		// resource() instead.
		$this->api_response->resource($rsc);
	}
}
~~~

So in short, you simply get the resource that the model set with
`$this->api_response->resource()`, you then modify the resource however you
wish, then reassign it to the response with
`$this->api_response->resource(Babble_API_Resource)`.

This is useful when you want to create simple CRUD functionality on top of your
models in your REST api but you want to add custom things to the resources that
the model assigned to the response.

Next Section: [Resources](resources)
