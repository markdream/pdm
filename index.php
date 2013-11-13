<?php
// +----------------------------------------------------------------------
// | Helper!!!
// +----------------------------------------------------------------------
// | Copyright (c) 2012 http://www.markdream.com All rights reserved.
// +----------------------------------------------------------------------
// | Link ( http://www.markdream.com )
// +----------------------------------------------------------------------
// | Author: Jxcent <jxcent@gmail.com>
// +----------------------------------------------------------------------
// $ File: index.php   2013-2-4 上午09:44:41	$


define ( 'APP_DEBUG', 0 );
define ( 'SITE_PATH', dirname ( __FILE__ ) );
define ( 'APP_NAME', 'pdb' );
define ( 'APP_PATH', './application/' );
//define ( 'P_LINK', $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']) );
define ( 'P_HOST', 'https://demo.markdream.com' );
define ( 'P_LINK', P_HOST . '/cgi' );

include 'config.inc.php';
include './fck/bin.php';
include './fck/gnu.php';
