# Controllers

## General Usage
Controllers should be located at `classes/Controller/Public/API/`. They must
extend either `Controller_API` or `Controller_API_Model` (which extends
Controller_API and in the Model CRUD section).

A controller responds to API requests by having controller methods for request
methods. For example, a GET request will be routed to action_get() and a PUT
request will be routed to action_put() etc.

Controller methods have access to the api request and response via
`$this->api_request` and `$this->api_response` respectively. You can
alternatively access these via API_Request::factory() and
API_Response::factory().

A controller method must set a response via the API response'
[set_response()](http://pilot.xxx/guide-api/API_Response#set_response)  method i.e.
`$this->api_response->set_response()`. The first param is a [message code]() and
the second is either an [API_Resource] object, a string message which will
create an API_Resource object internally, or NULL. Generally you will pass a
resource object as the response. These are documented in the
[Resources](resources.md) section.

## Model CRUD
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
}
~~~

That's it! So a few things to note...

- You must extend `Controller_API_Model`.
- You set the model you're working with using the $model property. The
  controller class name does _not_ need to match the model name.
- There's no need to add controller methods since they are defined in
  [Controller_API_Model]. You can, however, override them when needed.
