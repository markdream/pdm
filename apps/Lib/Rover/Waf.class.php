<?php
/**
 * [wpx] (C)2013 markdream Inc.
 * This is NOT a freeware, use is subject to license terms.
 *
 * $Id: Waf.class.php  2014-8-30 上午10:50:17 pony_chiang $
 */

defined ( 'WPX_NAME' ) or exit ( 'Access Denied' );

/**
 * 云体检通用漏洞防护补丁v1.1
 * 更新时间：2013-05-25
 * 功能说明：防护XSS,SQL,代码执行，文件包含等多种高危漏洞
 */

class Waf {
	
	private static $url_arr = array ('xss' => "\\=\\+\\/v(?:8|9|\\+|\\/)|\\%0acontent\\-(?:id|location|type|transfer\\-encoding)" );
	
	private static $args_arr = array ('xss' => "[\\'\\\"\\;\\*\\<\\>].*\\bon[a-zA-Z]{3,15}[\\s\\r\\n\\v\\f]*\\=|\\b(?:expression)\\(|\\<script[\\s\\\\\\/]|\\<\\!\\[cdata\\[|\\b(?:eval|alert|prompt|msgbox)\\s*\\(|url\\((?:\\#|data|javascript)", 

	'sql' => "[^\\{\\s]{1}(\\s|\\b)+(?:select\\b|update\\b|insert(?:(\\/\\*.*?\\*\\/)|(\\s)|(\\+))+into\\b).+?(?:from\\b|set\\b)|[^\\{\\s]{1}(\\s|\\b)+(?:create|delete|drop|truncate|rename|desc)(?:(\\/\\*.*?\\*\\/)|(\\s)|(\\+))+(?:table\\b|from\\b|database\\b)|into(?:(\\/\\*.*?\\*\\/)|\\s|\\+)+(?:dump|out)file\\b|\\bsleep\\([\\s]*[\\d]+[\\s]*\\)|benchmark\\(([^\\,]*)\\,([^\\,]*)\\)|(?:declare|set|select)\\b.*@|union\\b.*(?:select|all)\\b|(?:select|update|insert|create|delete|drop|grant|truncate|rename|exec|desc|from|table|database|set|where)\\b.*(charset|ascii|bin|char|uncompress|concat|concat_ws|conv|export_set|hex|instr|left|load_file|locate|mid|sub|substring|oct|reverse|right|unhex)\\(|(?:master\\.\\.sysdatabases|msysaccessobjects|msysqueries|sysmodules|mysql\\.db|sys\\.database_name|information_schema\\.|sysobjects|sp_makewebtask|xp_cmdshell|sp_oamethod|sp_addextendedproc|sp_oacreate|xp_regread|sys\\.dbms_export_extension)", 

	'other' => "\\.\\.[\\\\\\/].*\\%00([^0-9a-fA-F]|$)|%00[\\'\\\"\\.]" );
	
	// 记录WAF日志
	static function write_log($log) {
		$logpath = LOG_PATH . 'waf_'.date('ymd').'.txt';
		$log_f = fopen ( $logpath, "a+" );
		fputs ( $log_f, $log . "\r\n" );
		fclose ( $log_f );
	}
	
	// 初始加载
	static function load() {
		$referer = empty ( $_SERVER ['HTTP_REFERER'] ) ? array () : array ($_SERVER ['HTTP_REFERER'] );
		$query_string = empty ( $_SERVER ["QUERY_STRING"] ) ? array () : array ($_SERVER ["QUERY_STRING"] );
		waf::exec ( $query_string, Waf::$url_arr );
		waf::exec ( $_GET, Waf::$args_arr );
		waf::exec ( $_POST, Waf::$args_arr );
		waf::exec ( $_COOKIE, Waf::$args_arr );
		waf::exec ( $referer, Waf::$args_arr );
	}
	
	// 单个执行
	static function exec($arr, $v) {
		foreach ( $arr as $key => $value ) {
			if (! is_array ( $key )) {
				Waf::check ( $key, $v );
			} else {
				Waf::exec ( $key, $v );
			}
			
			if (! is_array ( $value )) {
				Waf::check ( $value, $v );
			} else {
				Waf::exec ( $value, $v );
			}
		}
	}
	
	// 内部子检查
	static function check($str, $v) {
		foreach ( $v as $key => $value ) {
			if (preg_match ( "/" . $value . "/is", $str ) == 1 || preg_match ( "/" . $value . "/is", urlencode ( $str ) ) == 1) {
				Waf::write_log ( "IP: " . $_SERVER ["REMOTE_ADDR"] . " - 时间: " . strftime ( "%Y-%m-%d %H:%M:%S" ) . " - 页面:" . $_SERVER ["PHP_SELF"] . " - 提交方式: " . $_SERVER ["REQUEST_METHOD"] . " - 提交数据: " . $str );
				waf::render ( '你被抓住了！你的行为已经记录在系统日志中！' );
				exit ();
			}
		}
	}
	
	// 渲染错误信息
	static function render($msg) {
		$content_url = WPX_CONTENT_URL;
		$html = <<<RENDER
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>WAF监控警告！</title>
<link rel="stylesheet" href="{$content_url}css/wpx.main.css" />
<style>
body{background:#BB3939}
.page-ew{background: #FFF;padding: 25px;}
</style>
</head>
<body>
<div class="page-ew" style="width:688px">
  <img class="f_l" src="{$content_url}images/misc/wpx_error.gif"/>
  <ul>
    <li style="font-size:16px; line-height: 28px;"><b>{$msg}</b></li>
    <li>&nbsp; </li>
  </ul>
  <p style="text-align:center; color:#3D3D3D">&copy;<a href="http://www.markdream.com/?pref=wpx_WAF1.0">markdream</a> - WAF SYSTEM</p>

</div>
</body>
</html>
RENDER;
		
		echo $html;
	}
}