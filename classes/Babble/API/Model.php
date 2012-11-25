<?php defined('SYSPATH') or die('No direct script access.');

/**
 * abstract class for interacting with a model using specific CRUD'sh calls.
 * it's a layer between our API and whichever ORM/ModelLayer  our system happens to be using.
 * @abstract
 */
abstract class Babble_API_Model {
	/**
	 * @var string the model we're working with
	 * @access protected
	 */
	protected $model = null;

	/**
	 * @var array the relevant model fields. others are unimportant in regards to the API.
	 * @access protected
	 */
	protected $model_fields = array();

	public static function factory($model)
	{
		// if no driver passed pull from config or default to 'ORM'
		$cfg_driver = Kohana::$config->load('babble.model_driver');
		$default_class = 'API_Model_Driver_'.($cfg_driver ? $cfg_driver : 'ORM');
		$model_class = 'API_Model_Driver_'.ucfirst($model);
		$class = class_exists($model_class) ? $model_class : $default_class;
		return new $class($model);
	}

	/**
	 * constructor
	 */
	protected final function __construct($model)
	{
		$this->model = $model;
	}

	/**
	 * set model return fields
	 * @access public
	 * @param array $fields the model return fields
	 * @return self
	 */
	public function set_model_fields(array $fields = array())
	{
		$this->model_fields = $fields;
		return $this;
	}

	/**
	 * get a list of models
	 * @access public
	 * @abstract
	 * @TODO doc params
	 * @return Babble_API_Resource_Collection List of resources
	 * @throws API_Model_Exception Error
	 */
	abstract public function get_list($page = 1, $limit = NULL, $sort = NULL, $search = NULL);

	/**
	 * get a spefic model
	 * @access public
	 * @abstract
	 * @param int $object_id An object id
	 * @return Babble_API_Resource A resource
	 * @throws API_Model_Exception Error
	 */
	abstract public function get($object_id);

	/**
	 * edit a model
	 * @access public
	 * @abstract
	 * @param int $object_id An object id
	 * @param Babble_API_Resource A resource object
	 * @return string A success message
	 * @throws API_Model_Exception Error
	 */
	abstract public function edit($object_id, Babble_API_Resource $resource);

	/**
	 * create a model
	 * @access public
	 * @abstract
	 * @param Babble_API_Resource A resource object
	 * @return string A success message
	 * @throws API_Model_Exception Error
	 */
	abstract public function add(Babble_API_Resource $resource);

	/**
	 * delete a model
	 * @access public
	 * @abstract
	 * @param int $object_id An object id
	 * @return string A success message
	 * @throws API_Model_Exception Error
	 */
	abstract public function delete($object_id);
}
