<?php
class AccountAction extends Action {
	
	private $user;
	
	function __construct() {
		parent::__construct ();
		$this->user = D ( 'User' );
	}
	
	function index() {
		$this->redirect ( 'login' );
	}
	
	function login() {
		if (session ( 'user' ) != null) {
			$this->redirect ( '/security' );
		}
		
		//判断是否被强制踢出
		$get = $this->_get ();
		if ($get ['relogin'] == 'true') {
			$this->assign ( 're_account', base64_decode ( $get ['account'] ) );
			$this->assign ( 're_refer', urldecode ( $get ['refer'] ) );
		}
		
		$this->display ();
	}
	
	function logon() {
		$refer = $_POST ['refer'];
		if ($this->user->create ()) {
			$map ['email'] = $this->user->email;
			$password = $this->user->pwd;
			
			$data = $this->user->field ( 'email,pwd,id' )->where ( $map )->find ();
			if ($data == null) {
				$this->error ( '用户名或密码错误' );
			}
			
			if ($data ['pwd'] !== shortGen ( $password )) {
				$this->error ( '用户名或密码错误' );
			}
			
			session ( 'user', $data );
			//设置登录时间 超时则销毁session 强制登录
			session ( 's_timeout', time () );
			$refer = (! empty ( $refer )) ? $refer : P_LINK . '/security/';
			header ( 'Location:' . $refer );
		} else {
			$this->error ( $this->user->getError () );
		}
	}
	
	//获取SSL状态
	function get_ssl() {
		echo is_ssl () ? 1 : 0;
	}
	//注销
	function logout() {
		session ( 'user', null );
		$this->redirect ( 'login' );
	}
	
	//注册
	function register() {
		if (IS_POST) {
			if ($this->user->create ()) {
				$_email = $this->user->email;
				$this->user->pwd = shortGen ( $this->user->pwd );
				if ($this->_checkEmail ( $_email ) == true) {
					$this->error ( '这个邮箱已经注册了' );
				}
				if ($this->user->add () > 0) {
					$this->success ( $_email . ' ，你的帐号已成功注册!', 'login' );
				} else {
					$this->error ( '服务器繁忙！' );
				}
			
			} else {
				$this->error ( $this->user->getError () );
			}
			die ();
		}
		$this->display ();
	}
	
	/**
	 * 检测邮箱是否存在
	 * @param string $email
	 */
	private function _checkEmail($email) {
		$map ['email'] = $email;
		$data = $this->user->where ( $map )->find ();
		if ($data != null)
			return true;
		return false;
	}
}