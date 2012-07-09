<?php

/**
 * base api model class. if an API controller wants to handle model CRUD operations, extend this class and set the {@see $this->model} property
 */
class Controller_API_Model extends Controller_API {
	/**
	 * @var string The model we're working with. to be set in child class.
	 */
	protected $model = null;

	/**
	 * @var API_Model An instance of an API_Model driver
	 */
	protected $api_model = null;

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
		if ( ! $this->api_model)
		{
			$this->api_model = API_Model::factory($this->model);
		}
	}

	/**
	 * get controller method
	 */
	public function action_get()
	{
		// get model's response
		$model_resp = $this->api_model->get(Request::current()->param('id'));

		// send encoded response
		$resp = $this->api_request->get_encoded_response($model_resp);
		$this->response->body($resp);
	}

	/**
	 * edit controller method
	 */
	public function action_edit()
	{
		// get model's response
		$model_resp = $this->api_model->edit(Request::current()->param('id'), Request::current()->post('model_data'));

		// send encoded response
		$resp = $this->api_request->get_encoded_response($model_resp);
		$this->response->body($resp);
	}

	/**
	 * add controller method
	 */
	public function action_add()
	{
		// get model's response
		$model_resp = $this->api_model->add(Request::current()->post('model_data'));

		// send encoded response
		$resp = $this->api_request->get_encoded_response($model_resp);
		$this->response->body($resp);
	}

	/**
	 * delete controller method
	 */
	public function action_delete()
	{
		// get model's response
		$model_resp = $this->api_model->delete(Request::current()->param('id'));

		// send encoded response
		$resp = $this->api_request->get_encoded_response($model_resp);
		$this->response->body($resp);
	}
}
