<?php defined('SYSPATH') or die('No direct script access.');

/**
 * abstract class for interacting with a model using specific CRUD'sh calls.
 * it's a layer between our API and whichever ORM/ModelLayer  our system happens to be using.
 * @abstract
 */
abstract class API_Model {
	/**
	 * @var string the model we're working with
	 */
	protected $model = null;

	/**
	 * load specific api model driver class
	 * @param string $model the model the class is working with
	 * @param string $model_driver the driver class to load
	 * @return API_Model a child instance of this class API_Model
	 * @access public
	 * @static
	 */
	public static function factory($model, $model_driver = null)
	{
		// if no driver passed pull from config or default to 'ORM'
		$cfg_driver = Kohana::$config->load('api.driver');
		$default_driver = $cfg_driver ? $cfg_driver : 'ORM';
		$model_driver = 'API_Model_'.ucfirst(($model_driver ? $model_driver : $default_driver));
		return new $model_driver($model);
	}

	/**
	 * constructor
	 */
	protected final function __construct($model)
	{
		$this->model = $model;
	}

	/**
	 * get a list of models
	 * @uses API_Request
	 * @uses API_Response
	 * @access public
	 * @abstract
	 */
	abstract public function get_list();

	/**
	 * get a spefic model
	 * @uses API_Request
	 * @uses API_Response
	 * @access public
	 * @abstract
	 */
	abstract public function get();

	/**
	 * edit a model
	 * @uses API_Request
	 * @uses API_Response
	 * @access public
	 * @abstract
	 */
	abstract public function edit();

	/**
	 * create a model
	 * @uses API_Request
	 * @uses API_Response
	 * @access public
	 * @abstract
	 */
	abstract public function add();

	/**
	 * delete a model
	 * @uses API_Request
	 * @uses API_Response
	 * @access public
	 * @abstract
	 */
	abstract public function delete();
}
