<?php
/**
 * 入口文件
 * @author nero
 * @date 2013/9/20
 */
define('ROOT_PATH', dirname(__FILE__));
define('FRAMEWORK', ROOT_PATH.DIRECTORY_SEPARATOR.'framework');
define('LOAD_ALL', 1);//加载类的开关
define('APPLICATION', ROOT_PATH.DIRECTORY_SEPARATOR.'application');
define('LIBS', ROOT_PATH.DIRECTORY_SEPARATOR.'libs');
define('_SEPARATOR', '_');//路由分割符
define('DEBUG', true);//使用环境test(测试)、production(生产)
define('CONFIG', APPLICATION.DIRECTORY_SEPARATOR.'config');

require FRAMEWORK.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'Base.php';
require FRAMEWORK.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'Bootstrap.php';

Bootstrap::run();//运行引导器