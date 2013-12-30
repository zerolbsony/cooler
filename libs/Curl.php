<?php
class CCurl extends CComponent 
{
private $url = '';
	private $protocol = 'http';
	private $params = array();
	private $referer = '';
	private $cookie = array();
	private $curl;
	private static $instance;

	function __construct($url, $protocol = 'http', $params = array(), $referer = '', $cookie = array())
	{
		$this->url = $url;
		$this->protocol = $protocol;
		$this->params = $params;
		$this->referer = $referer;
		$this->cookie = $cookie;
		$this->_init();	
	}
	
	private function _init()
	{
		$curl = curl_init();
		$this->curl = $curl;
		curl_setopt($curl, CURLOPT_URL, $this->url);
		curl_setopt($curl, CURLOPT_HEADER, FALSE);
		curl_setopt($curl, CURLOPT_ENCODING, 'gzip');
		curl_setopt($curl, CURLOPT_TIMEOUT, 130);
		curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0;)');
		
		if(!empty($this->params)){
			curl_setopt($curl, CURLOPT_POST, count($this->params)) ;
			foreach($this->params as $key => $val)
			{
				$tmp[] = "$key=$val";
			}
			curl_setopt($curl, CURLOPT_POSTFIELDS, implode('&', $tmp));
			unset($tmp);
		}
		if(!empty($this->referer))
			curl_setopt($curl, CURLOPT_REFERER, $this->referer);
		//Ҫע��https
		if($this->protocol == 'https') {
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		}
	}
	
	public function _get()
	{
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);//��ֱ���������
		$content = curl_exec($this->curl);
		$response = curl_getinfo($this->curl);
		$response['content'] = $content;
		return $response;
	}
	
	private function _close()
	{
		curl_close($this->curl);
	}
	
	public function __destruct()
	{
		$this->_close();
		self::$instance = NULL;
	}
}
?>