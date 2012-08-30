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

		$this->api_model = API_Model::factory($this->model);
	}

	/**
	 * http get controller method.
	 */
	public function action_get()
	{
		// a get request with an id means return the resource is one object
		if ($this->api_request->request_resource_id)
		{
			$this->api_model->get();
		}
		// without an id means the resource is a list of objects
		else
		{
			$this->api_model->get_list();
		}
	}

	/**
	 * http put controller method
	 */
	public function action_put()
	{
		$this->api_model->add();
	}

	/**
	 * http post controller method
	 */
	public function action_post()
	{
		$this->api_model->edit();
	}

	/**
	 * http delete controller method
	 */
	public function action_delete()
	{
		$this->api_model->delete();
	}
}
