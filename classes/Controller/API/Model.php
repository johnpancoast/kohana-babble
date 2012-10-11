<?php

/**
 * base api model class. if an API controller wants to handle model CRUD
 * operations, extend this class and set the {@see $this->model} property. If you
 * just want API functionality not related to models, you can just have your API
 * controller extend Controller_API instead.
 */
class Controller_API_Model extends Controller_API {
	/**
	 * @var string The model we're working with. to be set in child class.
	 * @access protected
	 */
	protected $model = null;

	/**
	 * @var array the model fields to return
	 * @access protected
	 */
	protected $model_fields = array();

	/**
	 * @var API_Model An instance of an API_Model driver
	 * @access protected
	 */
	protected $api_model = null;

	/**
	 * ACL
	 */
	protected $access = array(
		// put, post, delete require write
		'action_put' => array('user-write'),
		'action_post' => array('user-write'),
		'action_delete' => array('user-write'),

		// all other methods require at least read
		'*' => array('user-read'),
	);

	/**
	 * called before anything else
	 * @access public
	 */
	public function before()
	{
		// must call parent before() methods
		parent::before();

		if ( ! $this->model)
		{
			throw new Exception('You must set a model to use api > model functionality.');
		}

		$this->api_model = API_Model::factory($this->model)->set_model_fields($this->model_fields);
	}

	/**
	 * http get controller method.
	 */
	public function action_get()
	{
		try
		{
			$req = $this->api_request;

			// a get request with an id means the resource is one object
			if ($req->kohana_request()->param('resource_id'))
			{
				$resp = $this->api_model->get($req->kohana_request()->param('resource_id'));
			}
			// without an id means the resource is a list of objects
			else
			{
				// TODO pass params
				$resp = $this->api_model->get_list();
			}

			$this->api_response->set_response('200-000', $resp);
		}
		catch (API_Model_Exception $e)
		{
			throw new API_Response_Exception($e->getMessage(), $e->get_response_code());
		}
	}

	/**
	 * http put controller method
	 */
	public function action_put()
	{
		try
		{
			$req = $this->api_request;

			// if we have an id, we're editing an existing id otherwise, it's an add
			// TODO this should actually check to see if the object exists, then add/edit accordingly.
			if ($req->kohana_request()->param('resource_id'))
			{
				if ($this->api_model->edit($req->kohana_request()->param('resource_id'), $req->get_request_decoded()))
				{
					$this->api_response->set_response('200-000');
				}
			}
			else
			{
				$resource_id = $this->api_model->add($req->get_request_decoded());
				if ($resource_id)
				{
					$this->api_response->set_response('200-000', $resource_id);
				}
			}
		}
		catch (API_Model_Exception $e)
		{
			throw new API_Response_Exception($e->getMessage(), $e->get_response_code());
		}
	}

	/**
	 * http post controller method
	 */
	public function action_post()
	{
		try
		{
			$req = $this->api_request;

			// until the HTTP PATCH method becomes available, we should use POST
			// for partial updates of an existing resource
			if ($req->kohana_request()->param('resource_id'))
			{
				if ($this->api_model->edit($req->kohana_request()->param('resource_id'), $req->get_request_decoded()))
				{
					$resource_id = $req->kohana_request()->param('resource_id');
				}
			}
			else
			{
				$resource_id = $this->api_model->add($req->get_request_decoded());
			}

			if ($resource_id)
			{
				$this->api_response->set_response('200-000', $resource_id);
			}
		}
		catch (API_Model_Exception $e)
		{
			throw new API_Response_Exception($e->getMessage(), $e->get_response_code());
		}
	}

	/**
	 * http delete controller method
	 */
	public function action_delete()
	{
		try
		{
			if ($this->api_model->delete())
			{
				$this->api_response->set_response('200-000');
			}
		}
		catch (API_Model_Exception $e)
		{
			throw new API_Response_Exception($e->getMessage(), $e->get_response_code());
		}
	}
}
