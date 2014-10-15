<?php
class CUrlManager extends CComponent
{	
	const FORMAT_PATH = 'path';
	const FORMAT_GET = 'get';
	
	private $routeVar = 'r';
	private $urlFormat = self::FORMAT_GET;
	private $showScriptName = false;
	public $caseSensitive = true;
	
	public function __construct()
	{
		$this->init();
	}
	
	private function init()
	{
		$config = Base::app()->getConfig('components');
		foreach($config['urlManager'] as $key => $value)
			$this->$key = $value;
	}
	
	public function parseUrl($request)
	{
		if ($this->urlFormat == self::FORMAT_GET) {
			if((trim($_SERVER['REQUEST_URI'], '/') == ''))
				header('Location: ?'.$this->routeVar.'='.$this->rules['home']);
			if ( isset($_GET[$this->routeVar]) ){
				return $_GET[$this->routeVar];
			} elseif ( isset($_POST[$this->routeVar]) ) {
				return $_POST[$this->routeVar];
			} else 
				return '';
		} elseif ($this->urlFormat == self::FORMAT_PATH) {
			if((trim($_SERVER['REQUEST_URI'], '/') == ''))
				header('Location: '.$config['urlManager']['rules']['home']);
			return $_SERVER['REQUEST_URI'];//未完待续
		}
		
		return '';
	}
	
	public function parsePathInfo($pathInfo)
	{
		if($pathInfo==='')
			return;
		$segs=explode('/',$pathInfo.'/');
		$n=count($segs);
		for($i=0;$i<$n-1;$i+=2)
		{
			$key=$segs[$i];
			if($key==='') continue;
			$value=$segs[$i+1];
			if(($pos=strpos($key,'['))!==false && ($m=preg_match_all('/\[(.*?)\]/',$key,$matches))>0)
			{
				$name=substr($key,0,$pos);
				for($j=$m-1;$j>=0;--$j)
				{
					if($matches[1][$j]==='')
						$value=array($value);
					else
						$value=array($matches[1][$j]=>$value);
				}
				if(isset($_GET[$name]) && is_array($_GET[$name]))
					$value=CMap::mergeArray($_GET[$name],$value);
				$_REQUEST[$name]=$_GET[$name]=$value;
			}
			else
				$_REQUEST[$key]=$_GET[$key]=$value;
		}
	}
}