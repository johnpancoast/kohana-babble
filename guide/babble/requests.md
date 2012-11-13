# Requests

## Request Methods
You should have an understanding of [request
methods](http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html) and their
different uses. Knowing the differences between PUT and POST is important.

At present, Babble supports GET, PUT, POST, and DELETE. There are plans for
HEAD, and OPTIONS in the future. In addition, if the PATCH method gets approved
we'll add that.

## Routes
A Babble route looks like so.

`/route_path/<resource_controller>(/<resource_id>)`

* route_path - This is a config value `route_path`. It defaults to `api`.
* resource_controller - This should map to a controller in the
`classes/Controller/Public/API/` directory.
* resource_id - This is a "resource_id". It can be an ID, a title, whatever you wish.

So an example route is: `/api/user/3`.

Routes that match `route_path` (i.e., "/api") are first routed to the Babble
front controller at `classes/Controller/Public/APIFrontend.php`. This controller
initializes Babble then routes internally to the actual API controller in the
`classes/Controller/Public/API/` directory.

## Controllers
Controllers should be located at `classes/Controller/Public/API/`. They must
extend either `Controller_API` or `Controller_API_Model` (which extends
Controller_API and is discussed in the Model CRUD section).

A controller responds to API requests by having controller methods for request
methods. For example, a GET request will be routed to action_get() and a PUT
request will be routed to action_put() etc.

Controller methods have access to the api request and response via
`$this->api_request` and `$this->api_response` respectively. You can
alternatively access these via API_Request::factory() and
API_Response::factory().

A controller method must set a response via the API response'
[set_response()](http://pilot.xxx/guide-api/API_Response#set_response)  method i.e.
`$this->api_response->set_response()`. The first param is a [message code](messagecodes) and
the second is either an [API_Resource] object, a string message which will
create an API_Resource object internally, or NULL. Generally you will pass a
resource object as the response. These are documented in the
[Resources](resources.md) section.

## Model CRUD
Babble has the ability to quickly add model CRUD functionality. This has been
abstracted so you can put whatever ORM / model layer you want behind it. It
currently ships with support for Kohana ORM. 

View the [Model CRUD](modelcrud) section for more details.

Next Section: [Model CRUD](modelcrud)
