<?php
class CController extends CComponent 
{
	private $defaultAction = 'index';
	private $_id;
	
	public function __construct($id)
	{
		$this->_id = $id;
	}
	
	public function init()
	{
		
	}
	
	public function run($actionID)
	{
		if(($ca = $this->createAction($actionID)) !== null)
		{
			
		}else 
			throw new CException('error', 500);
	}
	
	public function actions()
	{
		return array();
	}
	
	public function createAction($actionID)
	{
		if($actionID==='')
			$actionID=$this->defaultAction;
		if(method_exists($this,'action'.$actionID) && strcasecmp($actionID,'s')) // we have actions method
			return new CInlineAction($this,$actionID);//待调整
		else
		{
			$action=$this->createActionFromMap($this->actions(),$actionID,$actionID);
			if($action!==null && !method_exists($action,'run'))
				throw new CException(sprintf('Action class %s must implement the "run" method.', get_class($action)));
			return $action;
		}
	}
	
	protected function createActionFromMap($actionMap,$actionID,$requestActionID,$config=array())
	{
		if(($pos=strpos($actionID,'.')) === false && isset($actionMap[$actionID]))
		{
			$baseConfig = is_array($actionMap[$actionID]) ? $actionMap[$actionID] : array('class'=>$actionID);
			return Base::createComponent($baseConfig, $this, $requestActionID);
		}
		else if($pos === false)
			return null;
	}
	
	public static function getController($controller)
	{
		if(!stripos($controller, 'Controller')){
			$controller = $controller.'Controller';
		}
		return ucfirst($controller);
	}
}
?>