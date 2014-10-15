<?php
/**
 * 程序运行入口类
 * @desc 通过引导器执行run来触发路由协议并最终执行控制器
 * @author nero
 * @date 2013/9/20
 */
class CApplication extends CComponent
{
	public $_requestUri = '';
	public $defaultController = 'SiteController';
	public $defaultAction = 'index';
	//cache for the controller is matched via url
	public $controllerMap = array();
	
	public function __construct($config=null)
	{
		Base::setApplication($this);

		// set basePath at early as possible to avoid trouble
		if(is_string($config))
			$config=require($config);

		//$this->preinit();

		$this->initHandlers();
		//$this->registerCoreComponents();

		$this->configure($config);
		//$this->attachBehaviors($this->behaviors);
		//$this->preloadComponents();

		//$this->init();
	}
	
	public function run()
	{
		$application = Base::app();
		$route = $application->parseRequest();
		self::runController($route);
	}
	
	private function parseRequest()
	{
		return $this->getUrlManager()->parseUrl(Base::app());
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
//			if(empty($route))
//				$route = $this->defaultAction;
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
			
		//echo $exception->getMessage();exit;
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
	
	public function configure($config)
	{
		if(is_array($config))
		{
			foreach($config as $key=>$value)
				$this->$key=$value;
		}
	}
	
	public function getConfig($type)
	{
		return $this->$type;
	}
}