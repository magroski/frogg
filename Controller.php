<?php

namespace Frogg;

use Phalcon\Mvc\Controller as PhalconController;
use Phalcon\Mvc\View as PhalconView;
use Detection\MobileDetect;

class Controller extends PhalconController {

	public $unauthorizedUrl = '/';
	public $authLabel		= 'auth';

	protected $publicActions = array();

	public function isAllowed($actionName, $auth){
		return in_array($actionName, $this->publicActions) || $auth;
	}

	public function isMobile(){
		$mobileDetector = new MobileDetect();
		return $mobileDetector->isMobile() && !$mobileDetector->isTablet();
	}

	public function login($auth){
		$this->auth = $auth;
	}

	public function getUnauthorizedUrl(){
		return $this->unauthorizedUrl;
	}

	public function forward($uri, $params = array()){
		$uriParts = explode('/', $uri);
		return $this->dispatcher->forward(
			array(
				'controller' => $uriParts[0],
				'action' => $uriParts[1],
				'params' => $params
			)
		);
	}

	public function redirect($url){
		$this->response->redirect($url);
		$this->view->disable();
	}

	public function partial($file, $params){
		$this->view->disableLevel(PhalconView::LEVEL_LAYOUT);
		return $this->view->getRender('partials', $file, $params);
	}

	private function encodedArray(array $array) : array{
		$encodedArray = [];
		foreach ($array as $key => $value) {
			if(is_array($value)){
				$encodedArray[$key] = $this->encodedArray($value);
			} else {
				if($value===null || is_numeric($value)){
					$encodedArray[$key] = $value;
				} else {
					$encodedArray[$key] = htmlspecialchars($value);
				}
			}
		}
		return $encodedArray;
	}

	public function getParam($name){
		$encodedParam = null;
		if($this->dispatcher->hasParam($name)) 	$encodedParam = $this->dispatcher->getParam($name);
		if($this->request->has($name)) 			$encodedParam = $this->request->get($name);
		if($this->request->hasPost($name)) 		$encodedParam = $this->request->getPost($name);
		if($this->request->hasPut($name)) 		$encodedParam = $this->request->getPut($name);
		if($this->request->hasQuery($name)) 	$encodedParam = $this->request->getQuery($name);

		if(is_array($encodedParam)){
			$encodedParam = $this->encodedArray($encodedParam);
		}

		if($encodedParam===null || is_numeric($encodedParam) || is_array($encodedParam)){
			return $encodedParam;
		} else {
			return htmlspecialchars($encodedParam);
		}
	}

	public function getDecodedParam($name){
		$decodedParam = null;
		if($this->dispatcher->hasParam($name)) 	$decodedParam = $this->dispatcher->getParam($name);
		if($this->request->has($name)) 			$decodedParam = $this->request->get($name);
		if($this->request->hasPost($name)) 		$decodedParam = $this->request->getPost($name);
		if($this->request->hasPut($name)) 		$decodedParam = $this->request->getPut($name);
		if($this->request->hasQuery($name)) 	$decodedParam = $this->request->getQuery($name);
		return $decodedParam;
	}

}