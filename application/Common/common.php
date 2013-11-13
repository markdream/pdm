<?php
function shortGen($string) {
	$base32 = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$hex = hash ( 'md5', $string . 'jxcent2013-05-28' );
	$hexLen = strlen ( $hex );
	$subHexLen = $hexLen / 8;
	$subHex = substr ( $hex, 0, 8 );
	$idx = 0x3FFFFFFF & (1 * ('0x' . $subHex));
	$out = '';
	for($j = 0; $j < 6; $j ++) {
		$val = 0x0000003D & $idx;
		$out .= $base32 [$val];
		$idx = $idx >> 5;
	}
	return $out;
}

/**
 * 时间轴函数，单位以unix时间戳计算
 * @param int $pubtime 发布时间
 */
function timeFormat($pubtime) {
	$time = time ();
	/** 如果不是同一年 */
	if (idate ( 'Y', $time ) != idate ( 'Y', $pubtime )) {
		return date ( 'Y年m月d日', $pubtime );
	}
	
	/** 以下操作同一年的日期 */
	$seconds = $time - $pubtime;
	$days = idate ( 'z', $time ) - idate ( 'z', $pubtime );
	
	/** 如果是同一天 */
	if ($days == 0) {
		/** 如果是一小时内 */
		if ($seconds < 3600) {
			/** 如果是一分钟内 */
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
	
	/** 如果是昨天 */
	if ($days == 1) {
		return '昨天' . date ( 'H:i', $pubtime );
	}
	
	/** 如果是前天 */
	if ($days == 2) {
		return '前天 ' . date ( 'H:i', $pubtime );
	}
	
	/** 如果是7天内 */
	if ($days < 7) {
		return $days . '天前';
	}
	
	/** 超过3天 */
	return date ( 'n月j日 H:i', $pubtime );
}

/**
 * 产生随机字串，可用来自动生成密码 默认长度6位 字母和数字混合
 * @param string $len 长度
 * @param string $type 字串类型
 * 0 字母 1 数字 其它 混合
 * @param string $addChars 额外字符
 * @return string
 */
function randCode($len = 6, $type = '', $addChars = '') {
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

/**
 * 扩展大U方法  自定生成规则  提升SEO优化
 */
function UX($url = '', $vars = '', $suffix = true, $redirect = false, $domain = false) {
	$tempStr = U ( $url, $vars, $suffix, $redirect, $domain );
	$url_model = C ( 'URL_MODEL' );
	if ($url_model == 2) {
		// Index/index
		$tempStr = str_ireplace ( C ( 'DEFAULT_MODULE' ) . '/' . C ( 'DEFAULT_ACTION' ), 'index', $tempStr );
		// index
		$tempStr = str_ireplace ( '/' . C ( 'DEFAULT_ACTION' ), '', $tempStr );
	} elseif ($url_model == 0) {
		// m=index&a=index
		$tempStr = str_ireplace ( '&' . C ( 'VAR_MODULE' ) . '=' . C ( 'DEFAULT_MODULE' ) . '&' . C ( 'VAR_ACTION' ) . '=' . C ( 'DEFAULT_ACTION' ), '', $tempStr );
		// &a=index
		$tempStr = str_ireplace ( '&' . C ( 'VAR_ACTION' ) . '=' . C ( 'DEFAULT_ACTION' ), '', $tempStr );
	}
	return $tempStr;
}

//输出安全的html
function h($text, $tags = null) {
	$text = trim ( $text );
	//完全过滤注释
	$text = preg_replace ( '/<!--?.*-->/', '', $text );
	//完全过滤动态代码
	$text = preg_replace ( '/<\?|\?' . '>/', '', $text );
	//完全过滤js
	$text = preg_replace ( '/<script?.*\/script>/', '', $text );
	
	$text = str_replace ( '[', '&#091;', $text );
	$text = str_replace ( ']', '&#093;', $text );
	$text = str_replace ( '|', '&#124;', $text );
	//过滤换行符
	//$text	=	preg_replace('/\r?\n/','',$text);
	//br
	//$text	=	preg_replace('/<br(\s\/)?'.'>/i','[br]',$text);
	$text = preg_replace ( '/(\[br\]\s*){10,}/i', '[br]', $text );
	//过滤危险的属性，如：过滤on事件lang js
	while ( preg_match ( '/(<[^><]+)( lang|on|action|background|codebase|dynsrc|lowsrc)[^><]+/i', $text, $mat ) ) {
		$text = str_replace ( $mat [0], $mat [1], $text );
	}
	while ( preg_match ( '/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i', $text, $mat ) ) {
		$text = str_replace ( $mat [0], $mat [1] . $mat [3], $text );
	}
	if (empty ( $tags )) {
		$tags = 'table|td|th|tr|i|b|u|strong|img|p|br|div|strong|em|ul|ol|li|dl|dd|dt|a';
	}
	//允许的HTML标签
	$text = preg_replace ( '/<(' . $tags . ')( [^><\[\]]*)>/i', '[\1\2]', $text );
	$text = preg_replace ( '/<\/(' . $tags . ')>/Ui', '[/\1]', $text );
	//过滤多余html
	$text = preg_replace ( '/<\/?(html|head|meta|link|base|basefont|body|bgsound|title|style|script|form|iframe|frame|frameset|applet|id|ilayer|layer|name|script|style|xml)[^><]*>/i', '', $text );
	//过滤合法的html标签
	while ( preg_match ( '/<([a-z]+)[^><\[\]]*>[^><]*<\/\1>/i', $text, $mat ) ) {
		$text = str_replace ( $mat [0], str_replace ( '>', ']', str_replace ( '<', '[', $mat [0] ) ), $text );
	}
	//转换引号
	while ( preg_match ( '/(\[[^\[\]]*=\s*)(\"|\')([^\2=\[\]]+)\2([^\[\]]*\])/i', $text, $mat ) ) {
		$text = str_replace ( $mat [0], $mat [1] . '|' . $mat [3] . '|' . $mat [4], $text );
	}
	//过滤错误的单个引号
	while ( preg_match ( '/\[[^\[\]]*(\"|\')[^\[\]]*\]/i', $text, $mat ) ) {
		$text = str_replace ( $mat [0], str_replace ( $mat [1], '', $mat [0] ), $text );
	}
	//转换其它所有不合法的 < >
	$text = str_replace ( '<', '&lt;', $text );
	$text = str_replace ( '>', '&gt;', $text );
	$text = str_replace ( '"', '&quot;', $text );
	//反转换
	$text = str_replace ( '[', '<', $text );
	$text = str_replace ( ']', '>', $text );
	$text = str_replace ( '|', '"', $text );
	//过滤多余空格
	$text = str_replace ( '  ', ' ', $text );
	return $text;
}

/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $suffix 截断显示字符
 * @param string $charset 编码格式
 * @return string
 */
function msubstr($str, $start = 0, $length, $suffix = false, $charset = "utf-8") {
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
	return $suffix ? $slice . '...' : $slice;
}

/**
 * 解密
 * @param string $str 密文
 * @param int $len 截取长度
 */
function decode($str, $len = 0) {
	$_str = GM::decode ( $str );
	if ($len != 0) {
		$_str = msubstr ( $_str, 0, $len );
	}
	return $_str;
}

/**
 * 加密
 */
function encode($str) {
	return GM::encode ( $str );
}


/**
 * 搜索关键字高亮函数
 * @param string $keyword 关键字
 * @param array $array 查询到的二位数组，相当于select查询出来的数组
 * @param array $fields 您所期望的字段高亮显示，对应数据库字段
 * @param boolean $isbold 是否加粗
 * @param string $color 颜色
 * @example
 * 为了避免在某些情况下我们要采用原生数据库字段，所以在采用您所期望的高亮的字段名，在模版中调用必须要加上“key_”+字段名 才能显示出高亮效果
 */
function keyword2red($keyword, $array, $fields, $isbold = false, $color = "red") {
	$replace_upstr = '<font color="' . $color . '">' . strtoupper($keyword) . '</font>';
	$replace_lowstr = '<font color="' . $color . '">' . strtolower($keyword) . '</font>';
	if ($isbold == true) {
		$replace_str = '<b>' . $replace_str . '</b>';
	}
	foreach ( $array as $k => $v ) {
		foreach ( $fields as $o => $p ) {
			$tmp=str_replace ( strtolower($keyword), $replace_lowstr, $array [$k] [$p] );
			$tmp=str_replace ( strtoupper($keyword), $replace_upstr,$tmp );
			$array [$k] ['key_' . $p] = $tmp;
		}
	}
	return $array;
}