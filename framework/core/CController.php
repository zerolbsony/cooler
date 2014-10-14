<?php
class CController extends CComponent 
{
	public function init()
	{
		
	}
	
	public function run($actionID)
	{
		$this->$actionID();
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