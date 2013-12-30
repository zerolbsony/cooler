<?php
class CApplication extends CComponent
{
	public $_requestUri = '';
	
	public function __construct()
	{
		$this->init();
	}
	
	protected function init()
	{
		set_exception_handler(array($this,'handleException'));//注册异常钩子
	}
	
	public function run()
	{
		$application = self::createComponent(get_class());
		$route = $application->parseRequest();
		self::runController($route);
	}
	
	private function parseRequest()
	{
		return self::getComponent('CUrlManager')->parseUrl(self::$_instance[get_class()]);
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
			throw new CException(404);
	}
	
	public function createController($route)
	{
		while(($pos=strpos($route,'/')) !== false)
		{
			$id = substr($route, 0, $pos);
			$className = ucfirst($id).'Controller';
			$classFile = APPLICATION.DIRECTORY_SEPARATOR.'control'.DIRECTORY_SEPARATOR.$className.'.php';
			if(is_file($classFile)){
				if(!class_exists($className,false)){
					require $classFile;
				}
				if(class_exists($className,false) && is_subclass_of($className,'CController')){
					$id[0]=strtolower($id[0]);
					return array(
						new $className($id),
						$this->parseActionParams($route),
					);
				}
			}
			return null;
		}
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
			return $pathInfo;
	}
	
	public function handleException($exception)
	{
		restore_exception_handler();

		$this->displayException($exception);
	}
	
	public function displayException($exception)
	{
		if(DEBUG)
		{
			echo '<h1>'.get_class($exception)."</h1>\n";
			echo '<p>'.$exception->getMessage().' ('.$exception->getFile().':'.$exception->getLine().')</p>';
			echo '<pre>'.$exception->getTraceAsString().'</pre>';
		}
		else
		{
			echo '<h1>'.get_class($exception)."</h1>\n";
			echo '<p>'.$exception->getMessage().'</p>';
		}
	}
}