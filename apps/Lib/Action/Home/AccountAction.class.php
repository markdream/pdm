<?php
/**
 * [pdm] (C)2014 markdream Inc.
 *
 * $Id: AccountAction.class.php 2014-10-24 下午12:51:19 pony_chiang $
 */
defined ( 'PDM_URL' ) or exit ( 'Access Denied' );
class AccountAction extends PDMAction {
	private $user;

	function __construct() {
		parent::__construct ();
		$this->user = M ( 'users' );
		if (session ( 'user' ) != null) {
			redirect ( pdm_ux ( 'Console/Index/index' ) );
		}
	}

	/**
	 * 帐号登录
	 */
	function login() {
		if (IS_POST) {
			$post = I ( 'post.' );
			if ($post ['email'] == '') {
				$this->error ( '邮件地址不能为空！' );
			}
			if ($post ['verification_code'] != $_SESSION ['verification_code']) {
				$this->error ( '你输入的验证码不正确！' );
			}
			if ($post ['password'] == '') {
				$this->error ( '密码不能为空！' );
			}
			
			$map ['email'] = $post ['email'];
			$data = $this->user->where ( $map )->field ( 'id,email,password,login_ip,login_time,email_auth,auth_code,timeout' )->find ();
			if ($data == null || $data ['email_auth'] == 0 || $post ['password'] != pdm_decode ( $data ['password'] )) {
				$this->error ( '帐号或密码错误！' );
			}
			// 更新登录信息
			$data_user ['login_count'] = array ( 'exp','login_count+1' );
			$data_user ['login_ip'] = get_client_ip ( 1 );
			$data_user ['login_time'] = NOW_TIME;
			$this->user->where ( $map )->save ( $data_user );
			
			unset ( $data ['password'] );
			session ( 'user', $data );
			session ( 'pdm_timeout', NOW_TIME );
			redirect ( pdm_ux ( 'Console/Index/index' ) );
		} else {
			$this->display ();
		}
	}

	/**
	 * 帐号注册
	 */
	function signin() {
		if (IS_POST) {
			$post = I ( 'post.' );
			if ($post ['email'] == '') {
				$this->error ( '邮件地址不能为空！' );
			}
			if ($post ['verification_code'] != $_SESSION ['verification_code']) {
				$this->error ( '你输入的验证码不正确！' );
			}
			
			$this->_check_email ( $post ['email'] );
			
			$data ['email'] = $post ['email'];
			$data ['register_time'] = NOW_TIME;
			$data ['register_ip'] = get_client_ip ( 1 );
			
			if ($this->user->add ( $data ) !== false) {
				$link = PDM_URL . '?c=account&a=authentication&sign=' . pdm_code ( $post ['email'] );
				$tpl = file_get_contents ( PDM_INC_PATH . 'ThirdParty/PHPMailer/templates/register_success.htm' );
				$tpl = str_replace ( '#EMAIL#', $data ['email'], $tpl );
				$tpl = str_replace ( '#SYSTEM_NAME#', PDM_NAME, $tpl );
				$tpl = str_replace ( '#DATE#', date ( 'Y年m月d日' ), $tpl );
				$tpl = str_replace ( '#LINK#', $link, $tpl );
				pdm_sendmail ( $data ['email'], '密码管理系统注册确认', $tpl );
				$this->success ( '帐号注册成功，请到你的邮件中确认激活！', '', 5 );
			} else {
				$this->error ( '帐号注册失败！' );
			}
		} else {
			$this->display ();
		}
	}

	/**
	 * 邮件认证
	 */
	function authentication() {
		if (IS_POST) {
			$post = I ( 'post.' );
			if ($post ['password'] == '') {
				$this->error ( '密码不能为空！' );
			}
			
			if ($post ['auth_code'] == '') {
				$this->error ( '加密KEY不能为空！' );
			}
			
			$id = pdm_code ( $post ['id'], 'DECODE' );
			
			if ($id == null) {
				$this->error ( PDM_ERROR );
			}
			
			$map ['id'] = $id;
			$data ['password'] = pdm_encode ( $post ['password'] );
			$data ['email_auth'] = 1;
			$data ['auth_code'] = pdm_encode ( md5 ( $post ['auth_code'] ) );
			
			if ($this->user->where ( $map )->save ( $data ) !== false) {
				// 清理多余帐号
				$_email = $this->user->where ( $map )->getField ( 'email' );
				$map_tmp ['email'] = $_email;
				$map_tmp ['email_auth'] = 0;
				$this->user->where ( $map_tmp )->delete ();
				
				$this->success ( '帐号激活成功！', pdm_ux ( 'login' ) );
			} else {
				$this->error ( '帐号激活失败！' );
			}
		} else {
			$sign = I ( 'sign' );
			$map ['email'] = pdm_code ( $sign, 'DECODE' );
			$map ['email_auth'] = 0;
			$data = $this->user->where ( $map )->field ( 'id,email' )->find ();
			if ($data == null) {
				$this->error ( '链接失效了！', pdm_ux ( 'login' ) );
			}
			$this->assign ( 'data', $data );
			$this->display ();
		}
	}

	/**
	 * 忘记密码
	 */
	function forgot() {
		if (IS_POST) {
			$post = I ( 'post.' );
			if ($post ['email'] == '') {
				$this->error ( 'Email不能为空！' );
			}
			
			$map ['email'] = $post ['email'];
			$map ['email_auth'] = 1;
			$id = $this->user->where ( $map )->getField ( 'id' );
			if ($id == null) {
				$this->error ( '无效的Email地址！' );
			}
			
			$link = PDM_URL . '?c=account&a=resetpassword&sign=' . pdm_code ( $post ['email'] );
			$tpl = file_get_contents ( PDM_INC_PATH . 'ThirdParty/PHPMailer/templates/forget_password.htm' );
			$tpl = str_replace ( '#EMAIL#', $post ['email'], $tpl );
			$tpl = str_replace ( '#SYSTEM_NAME#', PDM_NAME, $tpl );
			$tpl = str_replace ( '#DATE#', date ( 'Y年m月d日' ), $tpl );
			$tpl = str_replace ( '#LINK#', $link, $tpl );
			pdm_sendmail ( $post ['email'], '重设' . $post ['email'] . '在PDM的密码', $tpl );
			$this->success ( '一封重置密码的邮件已经发送到你的邮箱中，请确认查收！', '', 5 );
		} else {
			$this->display ();
		}
	}

	/**
	 * 重置密码
	 */
	function resetpassword() {
		if (IS_POST) {
			$post = I ( 'post.' );
			if ($post ['password'] == '') {
				$this->error ( '密码不能为空！' );
			}
			
			$id = pdm_code ( $post ['id'], 'DECODE' );
			if ($id == null) {
				$this->error ( PDM_ERROR );
			}
			
			$map ['id'] = $id;
			$data ['password'] = pdm_encode ( $post ['password'] );
			
			if ($this->user->where ( $map )->save ( $data ) !== false) {
				$this->success ( '密码重置成功！', pdm_ux ( 'login' ) );
			} else {
				$this->error ( '密码重置失败！' );
			}
		} else {
			$sign = I ( 'sign' );
			$map ['email'] = pdm_code ( $sign, 'DECODE' );
			$map ['email_auth'] = 1;
			$data = $this->user->where ( $map )->field ( 'id,email' )->find ();
			if ($data == null) {
				$this->error ( '链接失效了！', pdm_ux ( 'login' ) );
			}
			$this->assign ( 'data', $data );
			$this->display ();
		}
	}

	/**
	 * 验证码
	 */
	function captcha() {
		// 图片大小全局配置,扩展请在此添加
		$conf = array ( 's' => array ( 'width' => 250,'height' => 40,'minSize' => 20,'maxSize' => 22,'wave' => true ),'m' => array ( 'width' => 130,'height' => 40,'minSize' => 14,'maxSize' => 16,'wave' => false ),'n' => array ( 'width' => 100,'height' => 30,'minSize' => 12,'maxSize' => 12,'wave' => false ) );
		$ini = $conf ['s'];
		
		import ( '@.Rover.Captcha' );
		$captcha = new Captcha ();
		$captcha->resourcesPath = 'wp-include/ThirdParty/Captcha';
		$captcha->width = $ini ['width'];
		$captcha->height = $ini ['height'];
		$captcha->fonts = array ( 'ERASDEMI' => array ( 'spacing' => 1,'minSize' => $ini ['minSize'],'maxSize' => $ini ['maxSize'],'font' => 'ERASDEMI.ttf' ),'CourierNewBold' => array ( 'spacing' => - 1,'minSize' => $ini ['minSize'],'maxSize' => $ini ['maxSize'],'font' => 'CourierNewBold.ttf' ) );
		$captcha->session_var = 'verification_code';
		
		if ($ini ['wave'] == true) {
			$captcha->Yperiod = 28;
			$captcha->Yamplitude = 8;
			$captcha->Xperiod = 18;
			$captcha->Xamplitude = 4;
		} else {
			$captcha->Yperiod = 0;
			$captcha->Yamplitude = 0;
			$captcha->Xperiod = 0;
			$captcha->Xamplitude = 0;
			$captcha->im_x = 20;
		}
		$captcha->lineWidth = 1;
		$captcha->im = imagecreatefromjpeg ( $captcha->resourcesPath . '/bg_' . mt_rand ( 0, 2 ) . '.jpg' );
		$captcha->scale = 3;
		$captcha->colors = array ( array ( 27,78,181 ),		// blue
		array ( 19,121,100 ),array ( 42,52,64 ),array ( 128,15,87 ),array ( 214,36,7 ) ); // red
		$captcha->shadowColor = array ( 255,255,255 );
		$captcha->CreateImage ();
	}

	/**
	 * 检查电子邮件注册情况
	 * 
	 * @param string $email
	 */
	private function _check_email($email) {
		$map ['email'] = $email;
		$map ['email_auth'] = 1;
		$id = $this->user->where ( $map )->getField ( 'id' );
		if ($id != null) {
			$this->error ( 'Email已经被注册了！' );
		}
	}
}