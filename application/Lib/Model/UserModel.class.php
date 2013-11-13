<?php
class UserModel extends Model{
	
	protected $_map = array(
        'se' =>'email', 
        'sp'  =>'pwd', 
    );
	
	protected $_validate = array(
	    array('email','require','邮箱不能为空！'), 
	    array('email','email','请输入正确的邮箱帐号！'),
	    array('pwd','require','密码不能为空！'),
 	);
 	
 	
 	protected $_auto = array ( 
    	array('register_time','time',1,'function'),
    	array('loginip','get_client_ip',2,'function'), // 对create_time字段在更新的时候写入当前时间戳
 	);
}