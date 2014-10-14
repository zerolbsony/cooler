<?php
class CApplication extends CComponent
{
	public $_requestUri = '';
	public $defaultController = 'SiteController';
	public $defaultAction = 'index';
	//cache for the controller is matched via url
	public $controllerMap = array();
	
	public function run()
	{
		$this->initHandlers();
		$application = self::createComponent(get_class());
		$route = $application->parseRequest();
		self::runController($route);
	}
	
	private function parseRequest()
	{
		return $this->getUrlManager()->parseUrl(self::$_instance[get_class()]);
	}
	
	private function getUrlManager()
	{
		return self::getComponent('CUrlManager');
	}
	
	public function getRequestUri()
	{
		if( isset($_SERVER['REQUEST_URI']) ) {
			$this->_requestUri = $_SERVER['REQUEST_URI'];
			if( !empty($_SERVER['HTTP_HOST']) ) {
				if( strpos($this->_requestUri, $_SERVER['HTTP_HOST']) !== false )
					$this->_requestUri = preg_replace('/^\w+:\/\/[^\/]+/','',$this->_requestUri);
			}
			else
				$this->_requestUri = preg_replace('/^(http|https):\/\/[^\/]+/i','',$this->_requestUri);
		}
		return $this->_requestUri;
	}
	
	public function runController($route)
	{
		if(($ca=$this->createController($route))!==null)
		{
			list($controller,$actionID)=$ca;
			$oldController=$this->_controller;
			$this->_controller=$controller;
			$controller->init();
			$controller->run($actionID);
			$this->_controller=$oldController;
		} else
			throw new CHttpException(404);
	}
	
	public function createController($route)
	{
		if(($route=trim($route,'/')) === '')
			$route = $this->defaultController;
		$caseSensitive = $this->getUrlManager()->caseSensitive;
		$route .= '/';
		while(($pos=strpos($route,'/')) !== false)
		{
			$id = substr($route, 0, $pos);
			if(!preg_match('/^\w+$/', $id))
				return null;
			if(!$caseSensitive)
				$id = strtolower($id);
			$route = (string)substr($route, $pos+1);
			$className = ucfirst($id).'Controller';
			$classFile = APPLICATION.DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.$className.'.php';
			if(isset($this->controllerMap[$id])){
				return array(
					self::createComponent($this->controllerMap[$id]),
					$this->parseActionParams($route),
				);
			}
			if(is_file($classFile)){
				if(!class_exists($className,false)){
					require $classFile;
				}
				if(class_exists($className,false) && is_subclass_of($className,'CController')){
					$id[0]=strtolower($id[0]);
					return array(
						new $className,
						$this->parseActionParams($route),
					);
				}
			}
		}
		return null;
	}
	
	private function parseActionParams($pathInfo)
	{
		if(($pos=strpos($pathInfo,'/'))!==false)
		{
			$manager=self::getComponent('CUrlManager');
			$manager->parsePathInfo((string)substr($pathInfo,$pos+1));
			$actionID=substr($pathInfo,0,$pos);
			return $manager->caseSensitive ? $actionID : strtolower($actionID);
		}
		else
			return $this->defaultAction;
	}
	
	protected function initHandlers()
	{
		if(ENABLE_EXCEPTION_HANDLER)
			set_exception_handler(array($this, 'handlerException'));
		/*if(ENABLE_ERROR_HANDLER)
			set_error_handler(array($this,'handlerError'),error_reporting());*/
	}
	
	/**
	 * 捕获异常钩子
	 * @param unknown_type $exception
	 */
	public function handlerException($exception)
	{
		restore_error_handler();
		restore_exception_handler();
		/*if(!headers_sent())
			header("HTTP/1.0 ".$exception->statusCode." ".$exception->getHttpHeader($exception->statusCode, get_class($exception)));*/
			
		echo $exception->getTraceAsString();
	}
	
	/**
	 * 捕获错误钩子
	 * @param unknown_type $exception
	 */
	public function handlerError()
	{
		restore_error_handler();
		restore_exception_handler();
		print debug_backtrace();
	}
}