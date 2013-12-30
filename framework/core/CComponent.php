<?php
abstract class CComponent
{
	protected static $_instance;//数组
	
	private function init()
	{
	}
	
	/**
	 * 获取一个实例
	 * @desc 不支持命名空间
	 */
	public static function createComponent($class=null)
	{
		if($class == null)
			return null;
		if(is_string($class)){
			$class = trim($class);
		}
		else if(is_array($class) && isset($class['class'])){
			$class = $class['class'];
		}
		else 
			return null;
			
		$args = func_get_args();
			
		if ( !isset( self::$_instance[$class] ) ) {
			if(($cnt=count($args)) < 4) {
				switch (count($args)) {
					case 2:
						self::$_instance[$class] = new $class($args[1]);
						break;
					case 3:
						self::$_instance[$class] = new $class($args[1], $args[2]);
						break;
					case 4:
						self::$_instance[$class] = new $class($args[1], $args[2], $args[3]);
						break;
					default:
						self::$_instance[$class] = new $class;
						break;
				}
			}
			else {
				unset($args[0]);
				$object = new ReflectionClass($class);
				$object=call_user_func_array(array($object,'__construct'),$args);
				self::$_instance[$class] = $object;
			}	
		}
		return self::$_instance[$class];
	}
	
	public static function getComponent($class)
	{
		if ( !isset( self::$_instance[$class] ) ) {
			return self::createComponent($class);
		}
		return self::$_instance[$class];
	}
	
	public function __destruct()
	{
	}
}
?>