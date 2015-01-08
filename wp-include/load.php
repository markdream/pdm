<?php
/**
 * [pdm] (C)2014 markdream Inc.
 *
 * $Id: load.php  2014-10-24 下午12:03:43 pony_chiang $
 */

defined ( 'PDM_URL' ) or exit ( 'Access Denied' );

require PDM_PATH . '/wp-config.php';

define ( 'PDM_SHOWTRACE', 1 );
define ( 'PDM_VERSION', 'v1.0.141024' );
define ( 'PDM_ERROR', '请求错误！' );
define ( 'PDM_NAME', '密码管理系统' );
define ( 'PDM_COPYRIGHT', 'markdream' );
define ( 'PDM_LIMIT', 10 );

// 加密字符
define ( 'PDM_MAIN_CODE', 'markdream' );

// 邮件设置
define ( 'PDM_STMP', 1 );
define ( 'PDM_STMP_HOST', 'smtp.qq.com' );
define ( 'PDM_STMP_PORT', 25 );
define ( 'PDM_STMP_USER', 'webmaster@markdream.com' );
define ( 'PDM_STMP_PASSWORD', '123456' );

//system variables
define ( 'PDM_CONTENT_PATH', PDM_PATH . '/wp-content/' );
define ( 'PDM_INC_PATH', PDM_PATH . '/wp-include/' );
//templates variables

define ( 'PDM_CONTENT_URL', PDM_URL . 'wp-content/' );

//change the framework default options
define ( 'APP_PATH', PDM_PATH . '/apps/' );
define ( 'VENDOR_PATH', PDM_INC_PATH . '/ThirdParty/' );
define ( 'RUNTIME_PATH', PDM_PATH . '/tmp/' );

require PDM_PATH . '/wp-include/ThinkPHP/ThinkPHP.php';
