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
		// add, delete, edit require write
		'action_add' => array('user-write'),
		'action_delete' => array('user-write'),
		'action_edit' => array('user-write'),

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
	 * call on a model action
	 * @access private
	 * @final
	 * @param string $method The method to call upon
	 * @uses parent::api_response
	 * @throws API_Response_Exception On various errors
	 */
	private final function model_action($method)
	{
		if ( ! in_array($method, array('get', 'edit', 'add', 'delete')))
		{
			throw new API_Response_Exception('invalid api model action method called', '-9000');
		}

		// call on model method. api response should be set there.
		$this->api_model->{$method}();

		// check that a response got set
		$response = $this->api_response->get_response();
		if ( ! $response || ! isset($response['code']))
		{
			throw new API_Response_Exception('no model response', '-9000');
		}

		// send out main response from encoded api response
		$this->response->body($this->api_response->get_encoded_response());
	}

	/**
	 * get controller method
	 */
	public function action_get()
	{
		$this->model_action('get');
	}

	/**
	 * edit controller method
	 */
	public function action_edit()
	{
		$this->model_action('edit');
	}

	/**
	 * add controller method
	 */
	public function action_add()
	{
		$this->model_action('add');
	}

	/**
	 * delete controller method
	 */
	public function action_delete()
	{
		$this->model_action('delete');
	}
}
