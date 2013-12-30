<?php
class Sphinx extends SphinxClient
{
	const _host = 'localhost';
	const _port = 9306;
	const _max_connect_time = 5;
	const _max_query_time = 3;
	static $_instance = NULL;
	
	private function __construct()
	{
		$this->SphinxClient();
		$this->_init();
	}
	
	private function _init()
	{
		$this->setServer(self::_host, self::_port);
		$this->SetConnectTimeout(self::_max_connect_time);
		
		$this->setMaxQueryTime(self::_max_query_time);//查询超时
	}
	
	public function Query($keyword, $index, $mode=SPH_MATCH_EXTENDED2)
	{
		$this->setMatchMode($mode);
		$result = parent::Query($keyword, $index);
		return $result;
	}
	
	public static function getInstance()
	{
		if(empty(self::$_instance))
		{
			self::$_instance = new Sphinx();
		}
		
		return self::$_instance;
	}
	
	public function __destruct()
	{
		parent::__destruct();
	}
}
?>