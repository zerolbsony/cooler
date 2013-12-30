<?php
/**
 * 引导器
 * 完成一次请求响应的初始化过程，预配置环境。
 * @author nero
 * @date 2013/9/20
 */
class Bootstrap extends CComponent
{	
	public static function run()
	{
		self::createComponent(get_class())->init();
		Base::createApplication(CONFIG.DIRECTORY_SEPARATOR.'main.php')->run();//加载默认配置
	}
	
	private function init()
	{
		if (DEBUG) {
			ini_set('display_errors', 'On');
			error_reporting(E_ALL ^ E_NOTICE);
		} else {
			ini_set('display_errors', 'Off');
			error_reporting(0);
		}
	}
}

