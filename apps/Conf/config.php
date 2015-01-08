<?php
/**
 * [pdm] (C)2014 markdream Inc.
 *
 * $Id: config.php  2014-10-24 下午12:08:21 pony_chiang $
 */

defined('PDM_URL') or exit('Access Denied');

return array(
	//debug
	'SHOW_PAGE_TRACE' => PDM_SHOWTRACE,

	//group settings
	'APP_GROUP_LIST' => 'Home,Console',
    'DEFAULT_GROUP' => 'Home',
	'TMPL_FILE_DEPR' => '_',
	'LOAD_EXT_FILE' => 'base',

	//error settings
    'TMPL_EXCEPTION_FILE' => PDM_INC_PATH.'/ErrorPage/exception.htm',
	'TMPL_ACTION_SUCCESS' => PDM_INC_PATH.'/ErrorPage/error.htm',
	'TMPL_ACTION_ERROR' => PDM_INC_PATH.'/ErrorPage/error.htm',

	//url & template settings
	'TMPL_TEMPLATE_SUFFIX'=>'.htm',
	'URL_CASE_INSENSITIVE'=>1,
	'URL_MODEL'=>0,
	'URL_HTML_SUFFIX'=>'',

	'TOKEN_ON' => 1,
 	'TOKEN_NAME' => 'o_o',
//	'TOKEN_RESET'=>true,

	'TMPL_STRIP_SPACE' => 1,

	//variables settings
	'VAR_GROUP'=>'m',
	'VAR_MODULE'=>'c',
	'VAR_ACTION'=>'a',

	
	//database settings
	'DB_HOST'=>DB_HOST,
	'DB_NAME'=>DB_NAME,
	'DB_USER'=>DB_USER,
	'DB_PWD'=>DB_PWD,
	'DB_PREFIX'=>DB_PREFIX,
	'DB_PORT'=>DB_PORT,

);
?>