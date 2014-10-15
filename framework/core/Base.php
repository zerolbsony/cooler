<?php
/**
 * 基础类
 * 完成基本的框架初始化工作。
 * @author nero
 * @date 2013/9/20
 */
class Base
{
	public static $classMap=array();//特殊类配置(非通用路径下)
	public static $enableIncludePath=true;
	private static $_app;
	private static $_imports=array();
	
	/**
	 * 加载非通用路径下的类
	 * @todo 待开发
	 * @author nero
	 * @param string $alias 类名称
	 * @param boolean $forceInclude 是否使用php.ini中的include配置
	 * @return string
	 */
	public static function import($alias,$forceInclude=false)
	{
		if(isset(self::$_imports[$alias]))  // previously imported
			return self::$_imports[$alias];

		if(class_exists($alias,false) || interface_exists($alias,false))
			return self::$_imports[$alias]=$alias;

		if(($pos=strrpos($alias,'\\'))!==false) // a class name in PHP 5.3 namespace format
		{
			$namespace=str_replace('\\','.',ltrim(substr($alias,0,$pos),'\\'));
			if(($path=self::getPathOfAlias($namespace))!==false)
			{
				$classFile=$path.DIRECTORY_SEPARATOR.substr($alias,$pos+1).'.php';
				if($forceInclude)
				{
					if(is_file($classFile))
						require($classFile);
					else
						throw new CException(Yii::t('yii','Alias "{alias}" is invalid. Make sure it points to an existing PHP file.',array('{alias}'=>$alias)));
					self::$_imports[$alias]=$alias;
				}
				else
					self::$classMap[$alias]=$classFile;
				return $alias;
			}
			else
				throw new CException(Yii::t('yii','Alias "{alias}" is invalid. Make sure it points to an existing directory.',
					array('{alias}'=>$namespace)));
		}

		if(($pos=strrpos($alias,'.'))===false)  // a simple class name
		{
			if($forceInclude && self::autoload($alias))
				self::$_imports[$alias]=$alias;
			return $alias;
		}

		$className=(string)substr($alias,$pos+1);
		$isClass=$className!=='*';

		if($isClass && (class_exists($className,false) || interface_exists($className,false)))
			return self::$_imports[$alias]=$className;

		if(($path=self::getPathOfAlias($alias))!==false)
		{
			if($isClass)
			{
				if($forceInclude)
				{
					if(is_file($path.'.php'))
						require($path.'.php');
					else
						throw new CException(Yii::t('yii','Alias "{alias}" is invalid. Make sure it points to an existing PHP file.',array('{alias}'=>$alias)));
					self::$_imports[$alias]=$className;
				}
				else
					self::$classMap[$className]=$path.'.php';
				return $className;
			}
			else  // a directory
			{
				if(self::$_includePaths===null)
				{
					self::$_includePaths=array_unique(explode(PATH_SEPARATOR,get_include_path()));
					if(($pos=array_search('.',self::$_includePaths,true))!==false)
						unset(self::$_includePaths[$pos]);
				}

				array_unshift(self::$_includePaths,$path);

				if(self::$enableIncludePath && set_include_path('.'.PATH_SEPARATOR.implode(PATH_SEPARATOR,self::$_includePaths))===false)
					self::$enableIncludePath=false;

				return self::$_imports[$alias]=$path;
			}
		}
		else
			throw new CException(Yii::t('yii','Alias "{alias}" is invalid. Make sure it points to an existing directory or file.',
				array('{alias}'=>$alias)));
	}
	
	/**
	 * 创建CApplication实例
	 * @author nero
	 * @param string $config 要加载的站点配置文件路径
	 * @return object
	 */
	public static function createApplication($config=null)
	{
		if(self::$_app === null)
			return new CApplication($config);
		else
			return self::app();
	}
	
	/**
	 * 获取CApplication实例
	 * @author nero
	 * @return object
	 */
	public static function app()
	{
		return self::$_app;
	}
	
	/**
	 * 设置CApplication实例,做了单例,防止重复
	 * @author nero
	 * @return object
	 */
	public static function setApplication($app)
	{
		if(self::$_app === null || $app === null)
			self::$_app = $app;
		else
			throw new CException('application can only be created once.');
	}
	
	/**
	 * 自定义自动加载对象
	 * @author nero
	 * @param string $className 类名称
	 * @return boolean
	 */
	public static function autoload($className)
	{
		// use include so that the error PHP file may appear
		if(isset(self::$classMap[$className]))
			include(self::$classMap[$className]);
		else if(isset(self::$_coreClasses[$className]))
			include(FRAMEWORK.self::$_coreClasses[$className]);
		else if(LOAD_ALL && isset(self::$_libClasses[$className]))
			include(LIBS.self::$_libClasses[$className]);
		else
		{
			// include class file relying on include_path
			if(strpos($className,'\\')===false)  // class without namespace
			{
				if(self::$enableIncludePath===false)
				{
					foreach(self::$_includePaths as $path)
					{
						$classFile=$path.DIRECTORY_SEPARATOR.$className.'.php';
						if(is_file($classFile))
						{
							include($classFile);
							if(YII_DEBUG && basename(realpath($classFile))!==$className.'.php')
								throw new CException(Yii::t('yii','Class name "{class}" does not match class file "{file}".', array(
									'{class}'=>$className,
									'{file}'=>$classFile,
								)));
							break;
						}
					}
				}
				else
					include($className.'.php');
			}
			else  // class name with namespace in PHP 5.3
			{
				//Not supported now.
			}
			return class_exists($className,false) || interface_exists($className,false);
		}
		return true;
	}
	
	/**
	 * 自定义注册自动加载对象
	 * @desc 用于临时设置自动加载
	 * @author nero
	 * @param string $callback 类名称
	 * @param boolean $append 是否追加加载
	 * @return boolean
	 */
	public static function registerAutoloader($callback, $append=false)
	{
		if($append)
		{
			self::$enableIncludePath=false;
			spl_autoload_register($callback);
		}
		else
		{
			spl_autoload_unregister(array('Base','autoload'));
			spl_autoload_register($callback);
			spl_autoload_register(array('Base','autoload'));
		}
	}
	
	//核心类路径配置
	private static $_coreClasses = array(
		'CApplication' => '/core/CApplication.php',
		'CComponent' => '/core/CComponent.php',
		'CController' => '/core/CController.php',
		'CError' => '/core/CError.php',
		'CException' => '/core/CException.php',
		'CHttpException' => '/core/CHttpException.php',
		'CEvent' => '/core/CEvent.php',
		'CModel' => '/core/CModel.php',
		'CMoudle' => '/core/CMoudle.php',
		'CUrlManager' => '/core/CUrlManager.php',
		'CView' => '/core/CView.php',
		'CAuthManager' => '/base/CAuthManager.php',
		'CCache' => '/base/CCache.php',
		'CDb' => '/base/CDb.php',
		'CProtocol' => '/base/CProtocol.php',
		'CLog' => '/base/CLog.php',
		'CSession' => '/base/CSession.php',
		'CUser' => '/base/CUser.php',
		'CValidation' => '/base/CValidation.php',
		'CWidgetFactory' => '/base/CWidgetFactory',
	);
	//扩展库类路径配置
	private static $_libClasses = array(
		'Common' => '/Common.php',
		'Curl' => '/Curl.php',
		'EMailer' => '/mailer/Emailer.php',
		'Snoopy' => '/Snoopy.php',
		'Sphinx' => '/Sphinx.php',
		'SphinxClient' => '/SphinxClient.php',
	);
}

spl_autoload_register(array('Base','autoload'));