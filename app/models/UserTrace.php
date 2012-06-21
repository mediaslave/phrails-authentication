<?php
/**
 * Namespace for models
 */
namespace net\mediaslave\authentication\app\models;
/**
 * PageBlock
 */
/**
 * ClassBlock
 */
class UserTrace extends \Model{


	/**
	 * Add rules for this model.
	 *
	 * @author
	 */
	public function init(){
		$Request = \Registry::get('pr-request');
		$Route = \Registry::get('pr-route');

		if(is_object($Route)){
			$this->name = $Route->name;
			$this->controller = $Route->controller;
			$this->action = $Route->action;
			$this->view_type = $Route->view_type;
			$this->namespace = $Route->namespace;
			$this->path = $Route->path;
		}


		$this->request_uri = $Request->server('REQUEST_URI');

		$this->params = $this->serialize($Request->get());
		$this->post = $this->serialize($Request->post());

	}

	/**
	 * Serialize the object to store in the db
	 * 
	 * @param mixed $mixed
	 * @return string
	 */
	private function serialize($mixed){
		return serialize($mixed);
	}
}