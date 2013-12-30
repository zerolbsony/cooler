<?php
class Common {
	//去除变量中数值多余空格
	public static function TRIM($value) {
		if (is_array ( $value )) {
			foreach ( $value as &$val ) {
				$val = self::TRIM ( $val );
			}
			return $value;
		} else {
			return trim ( $value );
		}
	}
	
	//转换ipv4或ipv6地址
	public static function inet_pton($ip) {
		# ipv4
		if (strpos ( $ip, '.' ) !== FALSE) {
			$ip = pack ( 'N', ip2long ( $ip ) );
		} # ipv6
		elseif (strpos ( $ip, ':' ) !== FALSE) {
			$ip = explode ( ':', $ip );
			$res = str_pad ( '', (4 * (8 - count ( $ip ))), '0000', STR_PAD_LEFT );
			foreach ( $ip as $seg ) {
				$res .= str_pad ( $seg, 4, '0', STR_PAD_LEFT );
			}
			$ip = pack ( 'H' . strlen ( $res ), $res );
		}
		return $ip;
	}
	
	//还原ipv4或ipv6的地址
	public static function inet_ntop($ip) {
		if (strlen ( $ip ) == 4) {
			// ipv4
			list ( , $ip ) = unpack ( 'N', $ip );
			$ip = long2ip ( $ip );
		} elseif (strlen ( $ip ) == 16) {
			// ipv6
			$ip = bin2hex ( $ip );
			$ip = substr ( chunk_split ( $ip, 4, ':' ), 0, - 1 );
			$ip = explode ( ':', $ip );
			$res = '';
			foreach ( $ip as $seg ) {
				while ( $seg {0} == '0' )
					$seg = substr ( $seg, 1 );
				if ($seg != '') {
					$res .= ($res == '' ? '' : ':') . $seg;
				} else {
					if (strpos ( $res, '::' ) === false) {
						if (substr ( $res, - 1 ) == ':')
							continue;
						$res .= ':';
						continue;
					}
					$res .= ($res == '' ? '' : ':') . '0';
				}
			}
			$ip = $res;
		}
		return $ip;
	}
	
	public function getIp()
	{	
		if (! empty ( $_SERVER ["HTTP_CLIENT_IP"] )) {
			$ip = $_SERVER ["HTTP_CLIENT_IP"];
		} elseif (! empty ( $_SERVER ["HTTP_X_FORWARDED_FOR"] )) {
			$ip = $_SERVER ["HTTP_X_FORWARDED_FOR"];
		} elseif (! empty ( $_SERVER ["REMOTE_ADDR"] )) {
			$ip = $_SERVER ["REMOTE_ADDR"];
		} else {
			$ip = NULL;
		}
		return $ip;
	}
}
?>