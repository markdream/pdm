<?php
/**
 * [pdm] (C)2014 markdream Inc.
 *
 * $Id: base.php  2014-10-24 下午12:07:07 pony_chiang $
 */

defined ( 'PDM_URL' ) or exit ( 'Access Denied' );


/**
 * [TP内置函数优化]扩展大U方法 
 * 自定生成规则  提升SEO优化
 * 不考虑分组  分组情况下无需特别优化
 */
function pdm_ux($url = '', $vars = '', $suffix = true, $redirect = false, $domain = false) {
	$uri = U ( $url, $vars, $suffix, $redirect, $domain );
	$url_model = C ( 'URL_MODEL' );
	$var_dm = C ( 'DEFAULT_MODULE' );
	$var_da = C ( 'DEFAULT_ACTION' );
	$var_mod = C ( 'VAR_MODULE' );
	$var_act = C ( 'VAR_ACTION' );
	
	if ($url_model == 2) {
		// Index/index
		$uri = str_ireplace ( $var_dm . '/' . $var_da, $var_dm, $uri );
		// index
		$uri = str_ireplace ( '/' . $var_da, '', $uri );
	} elseif ($url_model == 0) {
		// m=index&a=index
		$uri = str_ireplace ( '&' . $var_mod . '=' . $var_dm . '&' . $var_act . '=' . $var_da, '', $uri );
		// &a=index
		$uri = str_ireplace ( '&' . $var_act . '=' . $var_da, '', $uri );
	}
	return $uri;
}

/**
 * [日期格式]普通日期格式化
 * @param int $unix
 * @param string $fr
 */
function pdm_dateformat($unix, $fr = 'Y-m-d') {
	return date ( $fr, $unix );
}

/**
 * [日期格式]时间轴函数，单位以unix时间戳计算
 * @param int $pubtime 发布时间
 * @return string
 */
function pdm_time_format($pubtime) {
	$time = NOW_TIME;
	if (idate ( 'Y', $time ) != idate ( 'Y', $pubtime )) {
		return date ( 'Y-m-d', $pubtime );
	}
	$seconds = $time - $pubtime;
	$days = idate ( 'z', $time ) - idate ( 'z', $pubtime );
	if ($days == 0) {
		if ($seconds < 3600) {
			if ($seconds < 60) {
				if (3 > $seconds) {
					return '刚刚';
				} else {
					return $seconds . '秒前';
				}
			}
			return intval ( $seconds / 60 ) . '分钟前';
		}
		return idate ( 'H', $time ) - idate ( 'H', $pubtime ) . '小时前';
	}
	if ($days == 1) {
		return '昨天' . date ( 'H:i', $pubtime );
	}
	if ($days == 2) {
		return '前天 ' . date ( 'H:i', $pubtime );
	}
	return date ( 'n月j日 H:i', $pubtime );
}

/**
 * [字符串处理]字符串截取，支持中文和其他编码
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $suffix 截断显示字符
 * @param string $charset 编码格式
 * @return string
 */
function pdm_substr($str, $start = 0, $length, $suffix = false, $charset = "utf-8") {
	if (function_exists ( "mb_substr" ))
		$slice = mb_substr ( $str, $start, $length, $charset );
	elseif (function_exists ( 'iconv_substr' )) {
		$slice = iconv_substr ( $str, $start, $length, $charset );
		if (false === $slice) {
			$slice = '';
		}
	} else {
		$re ['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$re ['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$re ['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$re ['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all ( $re [$charset], $str, $match );
		$slice = join ( "", array_slice ( $match [0], $start, $length ) );
	}
	$fix = '';
	if (strlen ( $slice ) < strlen ( $str )) {
		$fix = '...';
	}
	return $suffix ? $slice . $fix : $slice;
}

/**
 * [字符串处理]生成短字符串最长6位
 * @return string
 */
function pdm_buildstr($str = '') {
	if ($str == '')
		$str = NOW_TIME . mt_rand ( 100, 999 );
	$base32 = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$hex = hash ( 'md5', $str );
	$hexLen = strlen ( $hex );
	$subHexLen = $hexLen / 8;
	$subHex = substr ( $hex, 0, 8 );
	$idx = 0x3FFFFFFF & (1 * ('0x' . $subHex));
	for($j = 0; $j < 6; $j ++) {
		$val = 0x0000003D & $idx;
		$out .= $base32 [$val];
		$idx = $idx >> 5;
	}
	return $out;
}

/**
 * [字符串处理]字符串加密、解密函数
 * @param	string	$txt		字符串
 * @param	string	$operation	ENCODE为加密，DECODE为解密，可选参数，默认为ENCODE，
 * @param	string	$expiry		过期时间  默认为2小时
 * @return	string
 */
function pdm_code($string, $operation = 'ENCODE', $expiry = 3600) {
	$key_length = 4;
	$key = md5 ( PDM_MAIN_CODE );
	$fixedkey = md5 ( $key );
	$egiskeys = md5 ( substr ( $fixedkey, 16, 16 ) );
	$runtokey = $key_length ? ($operation == 'ENCODE' ? substr ( md5 ( microtime ( true ) ), - $key_length ) : substr ( $string, 0, $key_length )) : '';
	$keys = md5 ( substr ( $runtokey, 0, 16 ) . substr ( $fixedkey, 0, 16 ) . substr ( $runtokey, 16 ) . substr ( $fixedkey, 16 ) );
	$string = $operation == 'ENCODE' ? sprintf ( '%010d', $expiry ? $expiry + time () : 0 ) . substr ( md5 ( $string . $egiskeys ), 0, 16 ) . $string : base64_decode ( substr ( $string, $key_length ) );
	$i = 0;
	$result = '';
	$string_length = strlen ( $string );
	for($i = 0; $i < $string_length; $i ++) {
		$result .= chr ( ord ( $string {$i} ) ^ ord ( $keys {$i % 32} ) );
	}
	if ($operation == 'ENCODE') {
		return $runtokey . str_replace ( '=', '', base64_encode ( $result ) );
	} else {
		if ((substr ( $result, 0, 10 ) == 0 || substr ( $result, 0, 10 ) - time () > 0) && substr ( $result, 10, 16 ) == substr ( md5 ( substr ( $result, 26 ) . $egiskeys ), 0, 16 )) {
			return substr ( $result, 26 );
		} else {
			return '';
		}
	}
}

/**
 * [直接跳转]
 * @param string $url
 */
function pdm_redirect($url) {
	header ( 'Location: ' . $url );
	exit ();
}

/**
 * [HTTP] 请求URL获取数据  需要curl组件支持
 * @param string $url 请求URL
 * @param array $params 参数
 * @param string $method 请求方式
 * @param boolean $multi 超时设置
 * @param array $extheaders 附件头信息
 */
function pdm_request($url, $params = array(), $method = 'GET', $multi = false, $extheaders = array()) {
	if (! function_exists ( 'curl_init' ))
		exit ( 'Need to open the curl extension' );
	$method = strtoupper ( $method );
	$ci = curl_init ();
	curl_setopt ( $ci, CURLOPT_USERAGENT, 'WPX-SERVER' );
	curl_setopt ( $ci, CURLOPT_CONNECTTIMEOUT, 3 );
	$timeout = $multi ? 30 : 3;
	curl_setopt ( $ci, CURLOPT_TIMEOUT, $timeout );
	curl_setopt ( $ci, CURLOPT_RETURNTRANSFER, true );
	curl_setopt ( $ci, CURLOPT_SSL_VERIFYPEER, false );
	curl_setopt ( $ci, CURLOPT_SSL_VERIFYHOST, false );
	curl_setopt ( $ci, CURLOPT_HEADER, false );
	$headers = ( array ) $extheaders;
	switch ($method) {
		case 'POST' :
			curl_setopt ( $ci, CURLOPT_POST, TRUE );
			if (! empty ( $params )) {
				if ($multi) {
					foreach ( $multi as $key => $file ) {
						$params [$key] = '@' . $file;
					}
					curl_setopt ( $ci, CURLOPT_POSTFIELDS, $params );
					$headers [] = 'Expect: ';
				} else {
					curl_setopt ( $ci, CURLOPT_POSTFIELDS, http_build_query ( $params ) );
				}
			}
			break;
		case 'DELETE' :
		case 'GET' :
			$method == 'DELETE' && curl_setopt ( $ci, CURLOPT_CUSTOMREQUEST, 'DELETE' );
			if (! empty ( $params )) {
				$url = $url . (strpos ( $url, '?' ) ? '&' : '?') . (is_array ( $params ) ? http_build_query ( $params ) : $params);
			}
			break;
	}
	curl_setopt ( $ci, CURLINFO_HEADER_OUT, TRUE );
	curl_setopt ( $ci, CURLOPT_URL, $url );
	if ($headers) {
		curl_setopt ( $ci, CURLOPT_HTTPHEADER, $headers );
	}
	
	$response = curl_exec ( $ci );
	curl_close ( $ci );
	return $response;
}

/**
 * 格式化秒数
 * @param int $secs 秒
 */
function pdm_format_time($secs) {
	if ($secs <= 60) {
		$format_str = '%S 秒';
	} elseif ($secs <= 3600) {
		$format_str = '%M 分 %S 秒';
	} else {
		$format_str = '%H 时 %M 分%s 秒';
	}
	return gmstrftime ( $format_str, $secs );
}

/**
 * 邮件发送
 * @param string $email 地址
 * @param string $subject 主题
 * @param string $body 内容
 * @author pony
 * 
 */
function pdm_sendmail($email, $subject = '', $body = '') {
	vendor ( 'PHPMailer.PHPMailerAutoload' );
	$mail = new PHPMailer ();
	$body = eregi_replace ( "[\]", '', $body );
	$mail->CharSet = "utf8";
	$mail->IsSMTP ();
	$mail->SMTPDebug = 0;
	$mail->SMTPAuth = true;
	$mail->Host = PDM_STMP_HOST;
	$mail->Port = PDM_STMP_PORT;
	$mail->Username = PDM_STMP_USER;
	$mail->Password = PDM_STMP_PASSWORD;
	$mail->SetFrom ( PDM_STMP_USER, PDM_NAME );
	$mail->AddReplyTo ( PDM_STMP_USER, PDM_NAME );
	$mail->Subject = $subject;
	$mail->MsgHTML ( $body );
	$mail->AddAddress ( $email, '' );
	if (! $mail->Send ()) {
		return false;
	} else {
		return true;
	}
}

/** 
 * 加密 
 * @param string 明文 
 * @param string 密文编码（base64/bin） 
 * @return string 密文 
 */
function pdm_encode($str, $key = PDM_MAIN_CODE, $code = 'bin') {
	$result = mcrypt_encrypt ( MCRYPT_RIJNDAEL_128, $key, $str, MCRYPT_MODE_CBC );
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
function pdm_decode($str, $key = PDM_MAIN_CODE, $code = "bin") {
	$ret = false;
	switch ($code) {
		case 'base64' :
			$str = base64_decode ( $str );
			break;
		case 'bin' :
		default :
	}
	
	if ($str !== false) {
		$ret = @mcrypt_decrypt ( MCRYPT_RIJNDAEL_128, $key, $str, MCRYPT_MODE_CBC );
		$ret = trim ( $ret );
	}
	return $ret;
}

/**
 * 随机字符串
 * @param int $len
 * @param int $type
 * @param string $addChars
 */
function pdm_rand_code($len = 6, $type = '', $addChars = '') {
	$str = '';
	switch ($type) {
		case 0 :
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
			break;
		case 1 :
			$chars = str_repeat ( '0123456789', 3 );
			break;
		case 2 :
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
			break;
		case 3 :
			$chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
			break;
		default :
			// 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
			$chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
			break;
	}
	if ($len > 10) { //位数过长重复字符串一定次数
		$chars = $type == 1 ? str_repeat ( $chars, $len ) : str_repeat ( $chars, 5 );
	}
	$chars = str_shuffle ( $chars );
	$str = substr ( $chars, 0, $len );
	return $str;
}

