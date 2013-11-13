<?php
$cnf= array(
	'URL_MODEL'=>2,
	'URL_CASE_INSENSITIVE'=>true,
	'URL_HTML_SUFFIX'=>'',

	'APP_GROUP_LIST'=>'Home,Security',
	'DEFAULT_GROUP'=>'Home',
	'TMPL_FILE_DEPR'=>'_',
	
	'SHOW_PAGE_TRACE'=>0,
	'TMPL_STRIP_SPACE'=>0,
	'SESSION_PREFIX'=>'2013-08-08 09:51:20'

);

$database_cnf	= require './config.db.php';
return array_merge($cnf,$database_cnf);
?>
