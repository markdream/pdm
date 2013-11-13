<?php
/**
 * 系统加密类
 * @author jxcent@gmail.com
 *
 */
class GM {
	
	private static $MCRYPT = MCRYPT_RIJNDAEL_128;
	private static $KEY = 'PonyChiangpdb$92';
	private static $MODE = MCRYPT_MODE_CBC;
	
	/** 
	 * 加密 
	 * @param string 明文 
	 * @param string 密文编码（base64/bin） 
	 * @return string 密文 
	 */
	public static function encode($str, $code = 'bin') {
		$result = mcrypt_encrypt ( GM::$MCRYPT, GM::$KEY, $str, GM::$MODE );
		switch ($code) {
			case 'base64' :
				$ret = base64_encode ( $result );
				break;
			case 'bin' :
			default :
				$ret = $result;
		}
		return $ret;
	}
	
	/** 
	 * 解密  
	 * @param string 密文 
	 * @param string 密文编码（base64/bin） 
	 * @return string 明文 
	 */
	public static function decode($str, $code = "bin") {
		$ret = false;
		
		switch ($code) {
			case 'base64' :
				$str = base64_decode ( $str );
				break;
			case 'bin' :
			default :
		}
		
		if ($str !== false) {
			$ret = @mcrypt_decrypt ( GM::$MCRYPT, GM::$KEY, $str, GM::$MODE);
			$ret = trim ( $ret );
		}
		
		return $ret;
	}
}  