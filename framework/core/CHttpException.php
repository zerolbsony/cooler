<?php
/**
 * http协议的异常类
 * 负责http协议的异常处理
 * @author nero
 * @date 2014/10/11
 */
class CHttpException extends CException
{
	/**
	 * @var integer HTTP status code, such as 403, 404, 500, etc.
	 */
	public $statusCode;

	/**
	 * Constructor.
	 * @param integer $status HTTP status code, such as 404, 500, etc.
	 * @param string $message error message
	 * @param integer $code error code
	 */
	public function __construct($status,$message=null,$code=0)
	{
		$this->statusCode=$status;
		parent::__construct($message,$code);
	}
	
	public static function getHttpHeader($httpCode, $replacement='')
	{
		$httpCodes = array(
			100 => 'Continue',
			101 => 'Switching Protocols',
			102 => 'Processing',
			118 => 'Connection timed out',
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			207 => 'Multi-Status',
			210 => 'Content Different',
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			307 => 'Temporary Redirect',
			310 => 'Too many Redirect',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Time-out',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested range unsatisfiable',
			417 => 'Expectation failed',
			418 => 'I’m a teapot',
			422 => 'Unprocessable entity',
			423 => 'Locked',
			424 => 'Method failure',
			425 => 'Unordered Collection',
			426 => 'Upgrade Required',
			449 => 'Retry With',
			450 => 'Blocked by Windows Parental Controls',
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway ou Proxy Error',
			503 => 'Service Unavailable',
			504 => 'Gateway Time-out',
			505 => 'HTTP Version not supported',
			507 => 'Insufficient storage',
			509 => 'Bandwidth Limit Exceeded',
		);
		if(isset($httpCodes[$httpCode]))
			return $httpCodes[$httpCode];
		else
			return $replacement;
	}
}
?>