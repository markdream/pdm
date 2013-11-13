<?php
class PrivacyModel extends Model{
	
	private $userid;
	
	function __construct(){
		parent::__construct();
		$session_user = session ( 'user' );
		$this->userid= $session_user ['id'];
	}
	
	protected $_validate = array(
		array('typeid','require','分类不能为空！'),
	    array('title','require','标题不能为空！')
 	);
	
	protected $_auto = array ( 
		array('tid','createId',1,'callback'), 
		array('title','h',1,'function') , 
	    array('password','encode',1,'function') , 
	    array('account','encode',1,'function'), 
	    array('optime','time',1,'function'), 
	    array('ip','get_client_ip',1,'function'), 
	    array('userid','getUserId',1,'callback'), 
	    array('description','h',3,'function'), 
 	);
 	
 	//获取当前登录用户ID
 	function getUserId(){
		return $this->userid;
 	}
 	
 	//生成表主键ID
 	function createId(){
 		return shortGen(get_client_ip().'_'.time().'_$_'.$this->userid.'_92x_'.mt_rand(100,999));
 	}
}