<?php
/**
 * [pdm] (C)2014 markdream Inc.
 *
 * $Id: ProfileAction.class.php 2014-10-24 下午06:41:56 pony_chiang $
 */
defined ( 'PDM_URL' ) or exit ( 'Access Denied' );
class ProfileAction extends CBaseAction {
	private $user, $password;

	function __construct() {
		parent::__construct ();
		$this->user = M ( 'users' );
		$this->password = M ( 'password' );
	}

	function index() {
		$map ['id'] = $this->uid;
		$data = $this->user->where ( $map )->field ( 'id,email,password,login_count,register_time,register_ip,login_count,timeout' )->find ();
		if (IS_POST) {
			$post = I ( 'post.' );
			if ($post ['opassword'] != '' || $post ['password'] != '') {
				if ($post ['opassword'] != pdm_decode ( $data ['password'] )) {
					$this->error ( '你的旧密码填写错误！' );
				}
				$data_user ['password'] = pdm_encode ( $post ['password'] );
			}
			
			$data_user ['timeout'] = $post ['timeout'];
			if ($data_user ['timeout'] < 1 || $data_user ['timeout'] > 15) {
				$this->error ( '你填写的超时时间不在1~15分钟之内！' );
			}
			if ($this->user->where ( $map )->save ( $data_user ) !== false) {
				$this->success ( '修改成功！' );
			} else {
				$this->error ( '修改失败！' );
			}
		} else {
			$map_secret ['user_id'] = $this->uid;
			$data ['password_sum'] = $this->password->where ( $map_secret )->count ();
			$this->assign ( 'data', $data );
			$this->display ();
		}
	}

	/**
	 * 注销
	 */
	function logout() {
		session ( 'user', null );
		redirect ( pdm_ux ( 'Home/Account/login' ) );
	}
}