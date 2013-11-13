<?php
class UserAction extends SBAction{
	private $user;
	
	function __construct(){
		parent::__construct();
		
		$this->user=D('user');
	}
	
	function index(){
		$map['id']=$this->userid;
		$data=$this->user->field(true)->where($map)->find();
		$this->assign('user',$data);
		$this->display();
		
	}
	
	function post(){
		$post=$this->_post();
		
		$map['id']=$this->userid;
		$password=$this->user->where($map)->getField('pwd');
		
		if($password!==shortGen($post['opwd'])){
			$this->error('你原来的密码是错的');
		}
		
		$npwd=trim($post['npwd']);
		$data['pwd']=shortGen($npwd);
		if($this->user->where($map)->save($data)!==false){
			$this->success('密码修改成功！');
		}else{
			$this->error('内部错误');
		}
	}
}