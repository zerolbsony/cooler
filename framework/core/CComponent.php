<?php
abstract class CComponent
{
	protected static $_instance;//数组

	public function __construct()
	{
	}
	
	/**
	 * 获取一个实例
	 * @desc 不支持命名空间
	 */
	public static function createComponent($class=null)
	{
		if ( !isset( self::$_instance[$class] ) ) {
			self::$_instance[$class] = new $class(); 
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